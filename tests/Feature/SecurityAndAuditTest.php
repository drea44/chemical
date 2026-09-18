<?php

namespace Tests\Feature;

use App\Models\Chemical;
use App\Models\ChemicalDailyUsage;
use App\Models\ChemicalLogDate;
use App\Models\ChemicalMonthlyBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $viewer;
    private User $stockManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'ADMIN')->first();
        $this->stockManager = User::where('role', 'STOCK_MANAGER')->first();
        $this->viewer = User::where('role', 'VIEWER')->first();
    }

    public function test_viewer_is_forbidden_from_all_transaction_write_endpoints(): void
    {
        $chemical = Chemical::first();
        $date = ChemicalLogDate::create([
            'log_date'     => '2026-04-01',
            'period_month' => '2026-04',
            'analyst_name' => 'Test Analyst',
        ]);

        $this->actingAs($this->viewer)
            ->deleteJson(route('transactions.chemicals.destroy', $chemical))
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->postJson(route('transactions.update-minimum-stock'), [
                'chemical_id'   => $chemical->id,
                'minimum_stock' => 50,
            ])
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->postJson(route('transactions.update-cell'), [
                'chemical_id' => $chemical->id,
                'log_date_id' => $date->id,
                'field'       => 'take_1',
                'value'       => 10,
            ])
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->postJson(route('transactions.update-balance'), [
                'chemical_id'  => $chemical->id,
                'period_month' => '2026-04',
                'field'        => 'saldo_awal',
                'value'        => 100,
            ])
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->postJson(route('transactions.update-chemical'), [
                'chemical_id' => $chemical->id,
                'field'       => 'chemical_name',
                'value'       => 'Hacked Name',
            ])
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->postJson(route('transactions.update-analyst'), [
                'log_date_id'  => $date->id,
                'analyst_name' => 'Hacked Analyst',
            ])
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->post(route('transactions.dates.store'), [
                'log_date'     => '2026-04-20',
                'analyst_name' => 'New Date',
            ])
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->delete(route('transactions.dates.destroy', $date))
            ->assertStatus(403);

        $this->actingAs($this->viewer)
            ->post(route('transactions.quick-add-chemical'), [
                'chemical_name' => 'Unauthorized Chemical',
                'unit'          => 'g',
                'period_month'  => '2026-04',
            ])
            ->assertStatus(403);
    }

    public function test_admin_cannot_deactivate_or_demote_themselves(): void
    {

        $response = $this->actingAs($this->admin)
            ->post(route('users.deactivate', $this->admin));
        $response->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        $this->assertEquals('active', $this->admin->fresh()->status);

        $response = $this->actingAs($this->admin)
            ->put(route('users.update', $this->admin), [
                'name'       => $this->admin->name,
                'email'      => $this->admin->email,
                'role'       => 'VIEWER',
                'status'     => 'active',
                'department' => $this->admin->department,
            ]);
        $response->assertSessionHas('error', 'Anda tidak dapat mengubah peran (role) akun Anda sendiri.');
        $this->assertEquals('ADMIN', $this->admin->fresh()->role);

        $response = $this->actingAs($this->admin)
            ->post(route('users.reset-password', $this->admin));
        $response->assertSessionHas('error', 'Anda tidak dapat mereset akun Anda sendiri dari menu ini.');
    }

    public function test_chemical_with_daily_usages_cannot_be_deleted_from_registry(): void
    {
        $chemical = Chemical::first();
        $date = ChemicalLogDate::create([
            'log_date'     => '2026-04-01',
            'period_month' => '2026-04',
            'analyst_name' => 'Usage Tester',
        ]);

        ChemicalDailyUsage::create([
            'chemical_id' => $chemical->id,
            'log_date_id' => $date->id,
            'take_1'      => 5.0,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('chemicals.destroy', $chemical));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('chemicals', ['id' => $chemical->id]);
    }

    public function test_quick_add_chemical_from_master_report_with_monthly_balances(): void
    {
        $response = $this->actingAs($this->admin)->post(route('transactions.quick-add-chemical'), [
            'chemical_name' => 'Master Report Test Chemical',
            'unit'          => 'g',
            'minimum_stock' => 15,
            'redirect_to'   => 'master-report',
            'months'        => [
                '2026-03' => 50,
                '2026-04' => 75,
            ],
        ]);

        $response->assertRedirectContains('/transactions/master-report');
        $this->assertDatabaseHas('chemicals', [
            'chemical_name' => 'Master Report Test Chemical',
            'unit'          => 'g',
            'minimum_stock' => 15,
        ]);

        $chem = Chemical::where('chemical_name', 'Master Report Test Chemical')->first();
        $this->assertNotNull($chem);
        $this->assertDatabaseHas('chemical_monthly_balances', [
            'chemical_id'  => $chem->id,
            'period_month' => '2026-03',
            'saldo_awal'   => 50,
        ]);
        $this->assertDatabaseHas('chemical_monthly_balances', [
            'chemical_id'  => $chem->id,
            'period_month' => '2026-04',
            'saldo_awal'   => 75,
        ]);
    }

    public function test_quick_add_chemical_from_warning_stock_with_minimum_stock(): void
    {
        $response = $this->actingAs($this->admin)->post(route('transactions.quick-add-chemical'), [
            'chemical_name' => 'Warning Stock Test Chemical',
            'unit'          => 'ml',
            'current_stock' => 5,
            'minimum_stock' => 20,
            'redirect_to'   => 'warning-stock',
        ]);

        $response->assertRedirectContains('/transactions/warning-stock');
        $this->assertDatabaseHas('chemicals', [
            'chemical_name' => 'Warning Stock Test Chemical',
            'unit'          => 'ml',
            'current_stock' => 5,
            'minimum_stock' => 20,
            'status'        => 'LOW',
        ]);
    }
}

