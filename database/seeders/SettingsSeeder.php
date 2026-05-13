<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'site.name', 'value' => 'Booking ERP', 'type' => 'string', 'group' => 'general', 'is_public' => 1],
            ['key' => 'site.timezone', 'value' => 'Asia/Phnom_Penh', 'type' => 'string', 'group' => 'general', 'is_public' => 1],
            ['key' => 'site.currency', 'value' => 'USD', 'type' => 'string', 'group' => 'general', 'is_public' => 1],
            ['key' => 'booking.default_status', 'value' => 'pending', 'type' => 'string', 'group' => 'bookings', 'is_public' => 0],
        ];

        foreach ($defaults as $d) {
            Setting::firstOrCreate(
                ['key' => $d['key'], 'company_id' => null],
                $d
            );
        }
    }
}
