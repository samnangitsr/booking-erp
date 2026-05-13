<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\RatePlan;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds enough customers, properties, room types, rate plans, and bookings
 * so the bespoke Bookings UI has real data to render on a fresh install.
 *
 * Idempotent: uses firstOrCreate / existing-row checks so re-running is safe.
 */
class BookingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (! $company) {
            $this->command?->warn('BookingDemoSeeder: no company found, skipping.');

            return;
        }

        $propertyType = PropertyType::first() ?? PropertyType::create([
            'name' => 'Hotel', 'slug' => 'hotel', 'status' => 'active',
        ]);
        $branches = Branch::orderBy('id')->get();
        $superAdmin = User::where('email', 'super@bookingerp.demo')->first();

        // 1. Customers ----------------------------------------------------
        $customerSeed = [
            ['Sophea', 'Chan',   'female', 'sophea.chan@example.com',   '+855 12 111 111', 'KH'],
            ['Dara',   'Lim',    'male',   'dara.lim@example.com',      '+855 12 222 222', 'KH'],
            ['Maya',   'Tran',   'female', 'maya.tran@example.com',     '+855 12 333 333', 'VN'],
            ['Sok',    'Pich',   'male',   'sok.pich@example.com',      '+855 12 444 444', 'KH'],
            ['Vibol',  'Heng',   'male',   'vibol.heng@example.com',    '+855 12 555 555', 'KH'],
            ['Linda',  'Nguyen', 'female', 'linda.nguyen@example.com',  '+855 12 666 666', 'VN'],
            ['Ravy',   'Sok',    'female', 'ravy.sok@example.com',      '+855 12 777 777', 'KH'],
            ['John',   'Smith',  'male',   'john.smith@example.com',    '+1  415 000 1111', 'US'],
            ['Mei',    'Wang',   'female', 'mei.wang@example.com',      '+86 138 000 0001', 'CN'],
            ['Akira',  'Tanaka', 'male',   'akira.tanaka@example.com',  '+81 90 0000 0001', 'JP'],
        ];

        foreach ($customerSeed as [$first, $last, $gender, $email, $phone, $nat]) {
            Customer::firstOrCreate(
                ['email' => $email],
                [
                    'customer_code' => 'C'.strtoupper(Str::random(5)),
                    'first_name' => $first,
                    'last_name' => $last,
                    'gender' => $gender,
                    'phone' => $phone,
                    'nationality' => $nat,
                    'address' => 'Cambodia',
                    'status' => 'active',
                ]
            );
        }

        // 2. Properties --------------------------------------------------
        $propertySeed = [
            ['code' => 'PROP001', 'name' => 'Phnom Penh Riverside Hotel',  'star' => 4, 'branch' => 0],
            ['code' => 'PROP002', 'name' => 'Angkor Heritage Resort',      'star' => 5, 'branch' => 1],
            ['code' => 'PROP003', 'name' => 'Sihanoukville Beach Resort',  'star' => 4, 'branch' => 2],
        ];

        $properties = collect();
        foreach ($propertySeed as $i => $p) {
            $prop = Property::firstOrCreate(
                ['property_code' => $p['code']],
                [
                    'company_id' => $company->id,
                    'property_type_id' => $propertyType->id,
                    'name' => $p['name'],
                    'slug' => Str::slug($p['name']),
                    'star_rating' => $p['star'],
                    'description' => 'Demo property for the Booking ERP system.',
                    'address' => $p['name'].' street, Cambodia',
                    'phone' => '+855 23 100 200',
                    'email' => strtolower(Str::slug($p['name'])).'@bookingerp.demo',
                    'check_in_time' => '14:00',
                    'check_out_time' => '12:00',
                    'min_check_in_age' => 18,
                    'is_featured' => $i === 0,
                    'approval_status' => 'approved',
                    'status' => 'active',
                ]
            );
            $properties->push($prop);
        }

        // 3. Room types + rate plans -------------------------------------
        $roomTypeMatrix = [
            ['Deluxe King',  'DLK', 2, 1, 'm²', 32, 95.00],
            ['Twin Standard', 'TWN', 2, 0, 'm²', 28, 75.00],
            ['Suite Family', 'SFM', 3, 2, 'm²', 55, 165.00],
        ];

        $ratePlanMatrix = [
            ['Room Only',          'RO', 'none',        false],
            ['Bed & Breakfast',    'BB', 'breakfast',   true],
            ['Half Board',         'HB', 'half_board',  true],
        ];

        $roomTypeByProperty = [];

        foreach ($properties as $property) {
            $roomTypeByProperty[$property->id] = collect();
            foreach ($roomTypeMatrix as [$name, $code, $maxAdults, $maxChildren, $unit, $size, $price]) {
                $rt = RoomType::firstOrCreate(
                    ['property_id' => $property->id, 'room_type_code' => $code],
                    [
                        'name' => $name,
                        'slug' => Str::slug($property->property_code.'-'.$code),
                        'description' => $name.' room at '.$property->name,
                        'max_adults' => $maxAdults,
                        'max_children' => $maxChildren,
                        'max_occupancy' => $maxAdults + $maxChildren,
                        'room_size' => $size,
                        'room_size_unit' => $unit,
                        'total_rooms' => 10,
                        'base_price' => $price,
                        'status' => 'active',
                    ]
                );
                $roomTypeByProperty[$property->id]->push($rt);

                foreach ($ratePlanMatrix as [$rpName, $rpCode, $meal, $refundable]) {
                    RatePlan::firstOrCreate(
                        ['property_id' => $property->id, 'room_type_id' => $rt->id, 'rate_plan_code' => $code.'-'.$rpCode],
                        [
                            'name' => $rpName,
                            'meal_plan' => $meal,
                            'payment_policy' => 'pay_at_property',
                            'is_refundable' => $refundable,
                            'status' => 'active',
                        ]
                    );
                }
            }
        }

        // 4. Bookings ----------------------------------------------------
        if (Booking::count() > 0) {
            return;
        }

        $customers = Customer::orderBy('id')->get();
        $today = Carbon::today();

        $statusMatrix = [
            ['pending',     'unpaid',   -7, 2,  0],
            ['confirmed',   'partial',  -6, 3,  150.00],
            ['confirmed',   'unpaid',   -3, 5,  0],
            ['checked_in',  'partial',  -1, 2,  90.00],
            ['checked_in',  'paid',     -2, 3,  220.00],
            ['checked_out', 'paid',    -10, -5, 380.00],
            ['checked_out', 'paid',    -14, -10, 420.00],
            ['cancelled',   'refunded', -8, -3, 0],
            ['no_show',     'paid',    -12, -10, 95.00],
            ['confirmed',   'partial',  2,  5,  100.00],
            ['confirmed',   'unpaid',   5,  9,  0],
            ['pending',     'unpaid',   8,  12, 0],
        ];

        $properties = $properties->values();
        foreach ($statusMatrix as $idx => [$bs, $ps, $offsetIn, $offsetOut, $paid]) {
            $property = $properties[$idx % $properties->count()];
            $branch = $branches->isNotEmpty() ? $branches[$idx % $branches->count()] : null;
            $customer = $customers[$idx % $customers->count()];
            $rt = $roomTypeByProperty[$property->id]->random();
            $unitPrice = (float) $rt->base_price;
            $rooms = 1 + ($idx % 2);
            $checkIn = $today->copy()->addDays($offsetIn);
            $checkOut = $today->copy()->addDays($offsetOut);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $lineTotal = $unitPrice * $rooms * $nights;

            $booking = new Booking([
                'company_id' => $company->id,
                'branch_id' => $branch?->id,
                'customer_id' => $customer->id,
                'property_id' => $property->id,
                'booking_no' => 'BK'.now()->format('ymd').strtoupper(Str::random(4)).$idx,
                'booking_date' => $checkIn->copy()->subDays(2),
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'nights' => $nights,
                'total_rooms' => $rooms,
                'total_adults' => $rt->max_adults * $rooms,
                'total_children' => 0,
                'subtotal' => $lineTotal,
                'discount_amount' => 0,
                'tax_amount' => round($lineTotal * 0.10, 2),
                'fee_amount' => 5.00,
                'grand_total' => $lineTotal + round($lineTotal * 0.10, 2) + 5.00,
                'paid_amount' => $paid,
                'due_amount' => max(0, $lineTotal + round($lineTotal * 0.10, 2) + 5.00 - $paid),
                'currency_code' => 'USD',
                'booking_source' => ['website', 'admin', 'mobile_app'][$idx % 3],
                'payment_status' => $ps,
                'booking_status' => $bs,
                'special_request' => $idx % 4 === 0 ? 'High-floor room please.' : null,
                'created_by' => $superAdmin?->id,
            ]);

            if ($bs === 'cancelled') {
                $booking->cancelled_at = now()->subDays(2);
                $booking->cancellation_reason = 'Customer changed plans';
            }
            $booking->save();

            $booking->items()->create([
                'property_id' => $property->id,
                'room_type_id' => $rt->id,
                'rate_plan_id' => null,
                'room_id' => null,
                'room_name' => $rt->name,
                'rate_plan_name' => 'Room Only',
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'nights' => $nights,
                'rooms_count' => $rooms,
                'adults' => $rt->max_adults * $rooms,
                'children' => 0,
                'unit_price' => $unitPrice,
                'total_price' => $lineTotal,
                'status' => in_array($bs, ['checked_in', 'checked_out', 'cancelled']) ? $bs : 'reserved',
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'old_status' => null,
                'new_status' => 'pending',
                'note' => 'Booking created',
                'changed_by' => $superAdmin?->id,
            ]);
            if (! in_array($bs, ['pending'])) {
                BookingStatusHistory::create([
                    'booking_id' => $booking->id,
                    'old_status' => 'pending',
                    'new_status' => $bs,
                    'note' => null,
                    'changed_by' => $superAdmin?->id,
                ]);
            }
        }
    }
}
