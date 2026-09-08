<?php

namespace Tests\Feature;

use App\Models\Chemical;
use App\Models\ChemicalCategory;
use App\Models\ChemicalLocation;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChemicalStockTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $stockManager;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('role', 'ADMIN')->first();
        $this->stockManager = User::where('role', 'STOCK_MANAGER')->first();
        $this->viewer = User::where('role', 'VIEWER')->first();
    }

    public function test_login_and_dashboard_access(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Total Chemicals');
    }

    public function test_master_data_categories(): void
    {
        $response = $this->actingAs($this->admin)->get('/categories');
        $response->assertStatus(200);
        $response->assertSee('Chemical Categories');

        // Create new category
        $createResponse = $this->actingAs($this->admin)->post('/categories', [
            'name'        => 'Cryogenic Liquids',
            'color'       => '#06b6d4',
            'description' => 'Substances stored at cryogenic temperatures',
            'status'      => 'active',
        ]);
        $createResponse->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('chemical_categories', ['name' => 'Cryogenic Liquids']);
    }

    public function test_master_data_suppliers(): void
    {
        $response = $this->actingAs($this->admin)->get('/suppliers');
        $response->assertStatus(200);
        $response->assertSee('Chemical Suppliers');

        // Create new supplier
        $createResponse = $this->actingAs($this->admin)->post('/suppliers', [
            'name'           => 'Apex Biochemical Ltd',
            'contact_person' => 'Sarah Connor',
            'email'          => 'sarah@apexbiochem.com',
            'phone'          => '+62 21 800 1234',
            'status'         => 'active',
        ]);
        $createResponse->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('chemical_suppliers', ['name' => 'Apex Biochemical Ltd']);
    }

    public function test_master_data_locations(): void
    {
        $response = $this->actingAs($this->admin)->get('/locations');
        $response->assertStatus(200);
        $response->assertSee('Storage Locations');

        // Create new location
        $createResponse = $this->actingAs($this->admin)->post('/locations', [
            'name'              => 'Cold Room B - Refrigerator 2',
            'building'          => 'Lab Building',
            'room'              => 'Room 102',
            'shelf'             => 'Shelf 4',
            'storage_type'      => 'Cold Room (2-8°C)',
            'temperature_range' => '2°C - 8°C',
            'status'            => 'active',
        ]);
        $createResponse->assertRedirect(route('locations.index'));
        $this->assertDatabaseHas('chemical_locations', ['name' => 'Cold Room B - Refrigerator 2']);
    }

    public function test_chemical_registration_creates_initial_stock_transaction(): void
    {
        $category = ChemicalCategory::first();
        $location = ChemicalLocation::first();

        $response = $this->actingAs($this->admin)->post('/chemicals', [
            'chemical_name'     => 'Test Reagent X',
            'cas_number'        => '123-45-6',
            'category_id'       => $category->id,
            'location_id'       => $location->id,
            'current_stock'     => 50.0,
            'minimum_stock'     => 10.0,
            'maximum_stock'     => 100.0,
            'unit'              => 'L',
            'supplier'          => 'Merck KGaA',
            'batch_number'      => 'BATCH-TEST-01',
            'received_date'     => now()->toDateString(),
            'expiry_date'       => now()->addMonths(6)->toDateString(),
            'hazard_class'      => 'Flammable Liquid',
            'physical_state'    => 'liquid',
        ]);

        $chemical = Chemical::where('chemical_name', 'Test Reagent X')->first();
        $this->assertNotNull($chemical);
        $response->assertRedirect(route('chemicals.show', $chemical));

        // Verify initial transaction in ledger
        $this->assertDatabaseHas('stock_transactions', [
            'chemical_id'      => $chemical->id,
            'transaction_type' => 'STOCK_IN',
            'quantity'         => 50.0,
            'stock_after'      => 50.0,
        ]);
    }

    public function test_stock_in_and_stock_out_ledger(): void
    {
        $chemical = Chemical::where('status', 'SAFE')->first();
        $initialStock = (float) $chemical->current_stock;

        // Stock In
        $inResponse = $this->actingAs($this->stockManager)->post(route('stock.in.process'), [
            'chemical_id'      => $chemical->id,
            'quantity'         => 10,
            'reference_number' => 'PO-2026-999',
            'reason'           => 'Restocking batch',
        ]);
        $inResponse->assertRedirect(route('stock.in'));

        $chemical->refresh();
        $this->assertEquals($initialStock + 10, $chemical->current_stock);

        // Stock Out
        $outResponse = $this->actingAs($this->stockManager)->post(route('stock.out.process'), [
            'chemical_id'      => $chemical->id,
            'quantity'         => 5,
            'reference_number' => 'EXP-2026-001',
            'reason'           => 'Lab experiment usage',
        ]);
        $outResponse->assertRedirect(route('stock.out'));

        $chemical->refresh();
        $this->assertEquals($initialStock + 5, $chemical->current_stock);
    }

    public function test_stock_out_fails_if_insufficient_stock(): void
    {
        $chemical = Chemical::first();
        $excessQuantity = $chemical->current_stock + 9999;

        $response = $this->actingAs($this->stockManager)->post(route('stock.out.process'), [
            'chemical_id'      => $chemical->id,
            'quantity'         => $excessQuantity,
            'reference_number' => 'TEST-FAIL',
            'reason'           => 'Overdraw attempt',
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_stock_adjustment(): void
    {
        $chemical = Chemical::first();

        $response = $this->actingAs($this->admin)->post(route('stock.adjustment.process'), [
            'chemical_id'    => $chemical->id,
            'adjusted_stock' => 25.5,
            'reason'         => 'Annual physical audit count discrepancy',
        ]);
        $response->assertRedirect(route('stock.adjustment'));

        $chemical->refresh();
        $this->assertEquals(25.5, $chemical->current_stock);
    }

    public function test_chemical_printable_label(): void
    {
        $chemical = Chemical::first();
        $response = $this->actingAs($this->viewer)->get(route('chemicals.label', $chemical));
        $response->assertStatus(200);
        $response->assertSee($chemical->chemical_name);
        $response->assertSee($chemical->chemical_code);
        $response->assertSee('Chemical Label');
    }

    public function test_role_authorization_viewer_cannot_perform_stock_in(): void
    {
        $chemical = Chemical::first();

        // Viewer should get 403 Forbidden
        $response = $this->actingAs($this->viewer)->post(route('stock.in.process'), [
            'chemical_id'      => $chemical->id,
            'quantity'         => 10,
            'reference_number' => 'UNAUTH',
            'reason'           => 'Hacking',
        ]);

        $response->assertStatus(403);
    }

    public function test_supplier_authorization_viewer_is_forbidden(): void
    {
        $supplier = Supplier::first();

        // Viewer cannot view create form
        $this->actingAs($this->viewer)->get(route('suppliers.create'))->assertStatus(403);

        // Viewer cannot store supplier
        $this->actingAs($this->viewer)->post(route('suppliers.store'), [
            'name'   => 'Unauth Supplier',
            'status' => 'active',
        ])->assertStatus(403);

        // Viewer cannot edit supplier
        $this->actingAs($this->viewer)->get(route('suppliers.edit', $supplier))->assertStatus(403);

        // Viewer cannot update supplier
        $this->actingAs($this->viewer)->put(route('suppliers.update', $supplier), [
            'name'   => 'Hacked Supplier',
            'status' => 'active',
        ])->assertStatus(403);

        // Viewer cannot delete supplier
        $this->actingAs($this->viewer)->delete(route('suppliers.destroy', $supplier))->assertStatus(403);
    }

    public function test_cannot_delete_chemical_with_transactions(): void
    {
        // Chemical with transactions
        $chemical = Chemical::has('stockTransactions')->first();
        $this->assertNotNull($chemical);

        $response = $this->actingAs($this->admin)->delete(route('chemicals.destroy', $chemical));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('chemicals', ['id' => $chemical->id]);
    }

    public function test_cannot_delete_category_with_assigned_chemicals(): void
    {
        $category = ChemicalCategory::has('chemicals')->first();
        $this->assertNotNull($category);

        $response = $this->actingAs($this->admin)->delete(route('categories.destroy', $category));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('chemical_categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_location_with_chemicals(): void
    {
        $location = ChemicalLocation::has('chemicals')->first();
        $this->assertNotNull($location);

        $response = $this->actingAs($this->admin)->delete(route('locations.destroy', $location));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('chemical_locations', ['id' => $location->id]);
    }

    public function test_reports_csv_export_validates_and_streams_csv(): void
    {
        // Valid inventory export
        $response = $this->actingAs($this->admin)->get(route('reports.export-csv', ['type' => 'inventory']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Valid movement export
        $response = $this->actingAs($this->admin)->get(route('reports.export-csv', ['type' => 'movement']));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Invalid export type fails validation
        $response = $this->actingAs($this->admin)->get(route('reports.export-csv', ['type' => 'malicious_type']));
        $response->assertSessionHasErrors('type');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/chemicals')->assertRedirect(route('login'));
        $this->get('/stock')->assertRedirect(route('login'));
        $this->get('/settings')->assertRedirect(route('login'));
        $this->get('/users')->assertRedirect(route('login'));
    }

    public function test_user_management_restricted_to_admin(): void
    {
        // Stock manager cannot view users
        $this->actingAs($this->stockManager)->get(route('users.index'))->assertStatus(403);
        $this->actingAs($this->stockManager)->get(route('users.create'))->assertStatus(403);

        // Viewer cannot view users
        $this->actingAs($this->viewer)->get(route('users.index'))->assertStatus(403);

        // Admin can view users
        $this->actingAs($this->admin)->get(route('users.index'))->assertStatus(200);
    }

    public function test_settings_restricted_to_admin(): void
    {
        $this->actingAs($this->stockManager)->get(route('settings.index'))->assertStatus(403);
        $this->actingAs($this->viewer)->get(route('settings.index'))->assertStatus(403);
        $this->actingAs($this->admin)->get(route('settings.index'))->assertStatus(200);
    }

    public function test_audit_trail_restricted_to_admin_and_auditor(): void
    {
        $this->actingAs($this->viewer)->get(route('audit-trail.index'))->assertStatus(403);
        $this->actingAs($this->stockManager)->get(route('audit-trail.index'))->assertStatus(403);
        $this->actingAs($this->admin)->get(route('audit-trail.index'))->assertStatus(200);

        $auditor = User::where('role', 'AUDITOR')->first();
        if ($auditor) {
            $this->actingAs($auditor)->get(route('audit-trail.index'))->assertStatus(200);
        }
    }
}
