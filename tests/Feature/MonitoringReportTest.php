<?php

namespace Tests\Feature;

use App\Models\Chemical;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('role', 'ADMIN')->first();
    }

    public function test_monitoring_reports_and_catalog_are_intact(): void
    {
        // Check catalog has all 250 items including Sarung tangan safety and Azometin
        $this->assertSame(250, Chemical::count());
        $this->assertNotNull(Chemical::where('chemical_name', 'Azometin')->first());
        $this->assertNotNull(Chemical::where('chemical_name', 'Sarung tangan safety')->first());

        // Check March transactions exist
        $marchTxnCount = StockTransaction::where('transaction_date', 'like', '2026-03%')->count();
        $this->assertGreaterThan(0, $marchTxnCount, 'March database transactions must not be empty');

        // Check April transactions exist
        $aprilTxnCount = StockTransaction::where('transaction_date', 'like', '2026-04%')->count();
        $this->assertGreaterThan(0, $aprilTxnCount, 'April database transactions must not be empty');

        // Check May transactions exist
        $mayTxnCount = StockTransaction::where('transaction_date', 'like', '2026-05%')->count();
        $this->assertGreaterThan(0, $mayTxnCount, 'May database transactions must not be empty');

        // Check June transactions exist
        $juneTxnCount = StockTransaction::where('transaction_date', 'like', '2026-06%')->count();
        $this->assertGreaterThan(0, $juneTxnCount, 'June database transactions must not be empty');

        // Test June Report page
        $responseJune = $this->actingAs($this->admin)->get('/reports?tab=monitoring_june');
        $responseJune->assertStatus(200);
        $responseJune->assertSee('Monitoring Data Juni 2026');
        $responseJune->assertSee('Sarung tangan safety');
        $responseJune->assertSee('Zinc sulfate heptahydrate');

        // Test May Report page
        $responseMay = $this->actingAs($this->admin)->get('/reports?tab=monitoring_may');
        $responseMay->assertStatus(200);
        $responseMay->assertSee('Monitoring Data Mei 2026');

        // Test April Report page
        $responseApril = $this->actingAs($this->admin)->get('/reports?tab=monitoring_april');
        $responseApril->assertStatus(200);
        $responseApril->assertSee('Monitoring Data April 2026');

        // Test March Report page
        $responseMarch = $this->actingAs($this->admin)->get('/reports?tab=monitoring_march');
        $responseMarch->assertStatus(200);
        $responseMarch->assertSee('Monitoring Data Maret 2026');

        // Test Export CSV for June
        $responseCsvJune = $this->actingAs($this->admin)->get('/reports/export-csv?type=monitoring_june');
        $responseCsvJune->assertStatus(200);
        $responseCsvJune->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Test Export CSV for May
        $responseCsvMay = $this->actingAs($this->admin)->get('/reports/export-csv?type=monitoring_may');
        $responseCsvMay->assertStatus(200);
        $responseCsvMay->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Test Export CSV for April
        $responseCsvApril = $this->actingAs($this->admin)->get('/reports/export-csv?type=monitoring_april');
        $responseCsvApril->assertStatus(200);
        $responseCsvApril->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Test Export CSV for March
        $responseCsvMarch = $this->actingAs($this->admin)->get('/reports/export-csv?type=monitoring_march');
        $responseCsvMarch->assertStatus(200);
        $responseCsvMarch->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
