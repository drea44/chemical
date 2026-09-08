<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'system_name',            'value' => 'Chemical Stock OS',    'type' => 'string',  'description' => 'Application name'],
            ['key' => 'organization',           'value' => 'Research Laboratory',  'type' => 'string',  'description' => 'Organization name'],
            ['key' => 'timezone',               'value' => 'Asia/Jakarta',         'type' => 'string',  'description' => 'System timezone'],
            ['key' => 'date_format',            'value' => 'd M Y',                'type' => 'string',  'description' => 'Date display format'],
            ['key' => 'low_stock_threshold',    'value' => '20',                   'type' => 'integer', 'description' => 'Low stock percentage threshold'],
            ['key' => 'critical_stock_threshold','value' => '10',                  'type' => 'integer', 'description' => 'Critical stock percentage threshold'],
            ['key' => 'expiry_warning_days',    'value' => '30',                   'type' => 'integer', 'description' => 'Days before expiry to show warning'],
            ['key' => 'low_stock_alert',        'value' => '1',                    'type' => 'boolean', 'description' => 'Enable low stock alerts'],
            ['key' => 'expiry_alert',           'value' => '1',                    'type' => 'boolean', 'description' => 'Enable expiry alerts'],
            ['key' => 'critical_stock_alert',   'value' => '1',                    'type' => 'boolean', 'description' => 'Enable critical stock alerts'],
            ['key' => 'email_notification',     'value' => '0',                    'type' => 'boolean', 'description' => 'Send email notifications'],
            ['key' => 'session_timeout',        'value' => '120',                  'type' => 'integer', 'description' => 'Session timeout in minutes'],
            ['key' => 'two_factor_auth',        'value' => '0',                    'type' => 'boolean', 'description' => 'Enable two-factor authentication'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
