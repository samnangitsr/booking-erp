<?php

namespace Database\Seeders;

use App\Models\AvailabilityCalendar;
use App\Models\BookingItem;
use App\Models\DailyRate;
use App\Models\Property;
use App\Models\RatePlan;
use App\Models\RoomType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * Seeds rate plans, daily rates for the next 60 days and availability rows
 * for each property/room-type so the bespoke calendar grids show real data.
 *
 * Idempotent: rate-plan/daily-rate/availability rows are upserted via
 * firstOrCreate / updateOrCreate.
 */
class RatesAvailabilityDemoSeeder extends Seeder
{
    public function run(): void
    {
        $properties = Property::with(['roomTypes'])->orderBy('id')->get();
        if ($properties->isEmpty()) {
            $this->command?->warn('RatesAvailabilityDemoSeeder: no properties found, skipping.');

            return;
        }

        $planTemplates = [
            ['code' => 'BAR', 'name' => 'Best Available Rate', 'meal_plan' => 'none', 'payment_policy' => 'pay_at_property', 'is_refundable' => true],
            ['code' => 'BNB', 'name' => 'Bed & Breakfast', 'meal_plan' => 'breakfast', 'payment_policy' => 'pay_at_property', 'is_refundable' => true],
            ['code' => 'NRF', 'name' => 'Non-Refundable Saver', 'meal_plan' => 'none', 'payment_policy' => 'pay_now', 'is_refundable' => false],
        ];

        $today = CarbonImmutable::today();
        $horizonDays = 60;

        foreach ($properties as $property) {
            foreach ($property->roomTypes as $rtIdx => $roomType) {
                foreach ($planTemplates as $tpl) {
                    $code = $tpl['code'].'-'.($roomType->code ?? 'RT'.$roomType->id);
                    $plan = RatePlan::firstOrCreate(
                        [
                            'property_id' => $property->id,
                            'rate_plan_code' => $code,
                        ],
                        [
                            'room_type_id' => $roomType->id,
                            'name' => $tpl['name'].' — '.$roomType->name,
                            'meal_plan' => $tpl['meal_plan'],
                            'payment_policy' => $tpl['payment_policy'],
                            'is_refundable' => $tpl['is_refundable'],
                            'status' => 'active',
                        ]
                    );

                    $base = max(20.0, (float) ($roomType->base_price ?? 75));
                    $bump = match ($tpl['code']) {
                        'BNB' => 1.15,
                        'NRF' => 0.9,
                        default => 1.0,
                    };

                    for ($d = $today; $d->lt($today->addDays($horizonDays)); $d = $d->addDay()) {
                        $weekend = in_array($d->dayOfWeek, [5, 6], true);
                        $price = round($base * $bump * ($weekend ? 1.18 : 1.0), 2);
                        DailyRate::updateOrCreate(
                            [
                                'rate_plan_id' => $plan->id,
                                'rate_date' => $d->toDateString(),
                            ],
                            [
                                'property_id' => $property->id,
                                'room_type_id' => $roomType->id,
                                'base_price' => $price,
                                'adult_price' => $price,
                                'child_price' => round($price * 0.5, 2),
                                'extra_bed_price' => round($price * 0.3, 2),
                                'currency_code' => 'USD',
                                'min_stay' => $weekend ? 2 : 1,
                                'max_stay' => 14,
                                'closed_to_arrival' => false,
                                'closed_to_departure' => false,
                                'stop_sell' => false,
                            ]
                        );
                    }
                }

                $totalRooms = max(1, (int) ($roomType->total_rooms ?? 4));
                for ($d = $today; $d->lt($today->addDays($horizonDays)); $d = $d->addDay()) {
                    $booked = (int) BookingItem::query()
                        ->where('room_type_id', $roomType->id)
                        ->whereDate('date_from', '<=', $d->toDateString())
                        ->whereDate('date_to', '>=', $d->toDateString())
                        ->sum('rooms');
                    $booked = min($totalRooms, $booked);
                    $blocked = in_array($d->dayOfWeek, [2, 4], true) ? min(1, max(0, $totalRooms - $booked - 1)) : 0;
                    $available = max(0, $totalRooms - $booked - $blocked);
                    AvailabilityCalendar::updateOrCreate(
                        [
                            'room_type_id' => $roomType->id,
                            'available_date' => $d->toDateString(),
                        ],
                        [
                            'property_id' => $property->id,
                            'total_rooms' => $totalRooms,
                            'booked_rooms' => $booked,
                            'blocked_rooms' => $blocked,
                            'available_rooms' => $available,
                            'stop_sell' => false,
                        ]
                    );
                }
            }
        }
    }
}
