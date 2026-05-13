<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard' => ['view'],
            'companies' => ['view','create','edit','delete'],
            'branches' => ['view','create','edit','delete'],
            'users' => ['view','create','edit','delete'],
            'roles' => ['view','create','edit','delete'],
            'partners' => ['view','create','edit','delete'],
            'partner_contracts' => ['view','create','edit','delete'],
            'properties' => ['view','create','edit','delete','approve'],
            'property_types' => ['view','create','edit','delete'],
            'property_contacts' => ['view','create','edit','delete'],
            'property_images' => ['view','create','edit','delete'],
            'amenities' => ['view','create','edit','delete'],
            'property_amenity' => ['view','create','edit','delete'],
            'property_policies' => ['view','create','edit','delete'],
            'nearby_places' => ['view','create','edit','delete'],
            'bed_types' => ['view','create','edit','delete'],
            'room_types' => ['view','create','edit','delete'],
            'rooms' => ['view','create','edit','delete'],
            'room_type_bed_type' => ['view','create','edit','delete'],
            'room_type_amenity' => ['view','create','edit','delete'],
            'room_type_images' => ['view','create','edit','delete'],
            'room_blocks' => ['view','create','edit','delete'],
            'cancellation_policies' => ['view','create','edit','delete'],
            'rate_plans' => ['view','create','edit','delete'],
            'daily_rates' => ['view','create','edit','delete'],
            'availability_calendars' => ['view','create','edit','delete'],
            'occupancy_rules' => ['view','create','edit','delete'],
            'child_age_policies' => ['view','create','edit','delete'],
            'taxes' => ['view','create','edit','delete'],
            'property_fees' => ['view','create','edit','delete'],
            'customers' => ['view','create','edit','delete'],
            'customer_documents' => ['view','create','edit','delete'],
            'bookings' => ['view','create','edit','delete','cancel','check_in','check_out'],
            'booking_items' => ['view','create','edit','delete'],
            'booking_item_daily_rates' => ['view','create','edit','delete'],
            'booking_status_histories' => ['view'],
            'guests' => ['view','create','edit','delete'],
            'check_in_out_logs' => ['view','create','edit','delete'],
            'payment_methods' => ['view','create','edit','delete'],
            'payments' => ['view','create','edit','delete'],
            'invoices' => ['view','create','edit','delete'],
            'invoice_items' => ['view','create','edit','delete'],
            'refunds' => ['view','create','edit','delete'],
            'commissions' => ['view','create','edit','delete'],
            'payouts' => ['view','create','edit','delete'],
            'payout_items' => ['view','create','edit','delete'],
            'promotions' => ['view','create','edit','delete'],
            'promotion_room_types' => ['view','create','edit','delete'],
            'coupons' => ['view','create','edit','delete'],
            'coupon_usages' => ['view'],
            'reviews' => ['view','create','edit','delete','approve'],
            'review_replies' => ['view','create','edit','delete'],
            'wishlists' => ['view','create','edit','delete'],
            'service_categories' => ['view','create','edit','delete'],
            'activities' => ['view','create','edit','delete'],
            'activity_schedules' => ['view','create','edit','delete'],
            'transfers' => ['view','create','edit','delete'],
            'service_bookings' => ['view','create','edit','delete'],
            'countries' => ['view','create','edit','delete'],
            'cities' => ['view','create','edit','delete'],
            'areas' => ['view','create','edit','delete'],
            'destinations' => ['view','create','edit','delete'],
            'report_exports' => ['view','create','edit','delete'],
            'activity_logs' => ['view'],
            'login_histories' => ['view'],
            'settings' => ['view','create','edit','delete'],
            'notification_templates' => ['view','create','edit','delete'],
            'profile' => ['view','edit'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['name' => "$module.$action", 'guard_name' => 'web'],
                    ['module' => $module, 'description' => ucfirst($action).' '.str_replace('_', ' ', $module)]
                );
            }
        }
    }
}
