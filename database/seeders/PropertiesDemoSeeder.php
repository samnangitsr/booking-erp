<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\NearbyPlace;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyPolicy;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use Illuminate\Database\Seeder;

/**
 * Hydrates the demo properties (already created by BookingDemoSeeder) with
 * amenities, photos, policies, nearby places and physical rooms so the
 * bespoke Properties + Rooms UI has real data on first install.
 *
 * Idempotent: every section short-circuits when the relation is already
 * populated, so re-running the seeder is safe.
 */
class PropertiesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $properties = Property::orderBy('id')->get();
        if ($properties->isEmpty()) {
            $this->command?->warn('PropertiesDemoSeeder: no properties found, skipping.');

            return;
        }

        $propertyAmenities = Amenity::where('amenity_type', 'property')->get();
        $roomAmenities = Amenity::where('amenity_type', 'room')->get();

        $photoLibrary = [
            'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1200&q=70',
            'https://images.unsplash.com/photo-1551918120-9739cb430c6d?w=1200&q=70',
            'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1200&q=70',
            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=70',
            'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&q=70',
            'https://images.unsplash.com/photo-1455587734955-081b22074882?w=1200&q=70',
        ];
        $roomPhotos = [
            'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1200&q=70',
            'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1200&q=70',
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=1200&q=70',
        ];

        $policyTemplates = [
            ['checkin', 'Check-in time', 'Check-in starts from 14:00. Early check-in subject to availability.'],
            ['checkout', 'Check-out time', 'Check-out by 12:00. Late check-out subject to availability and additional charges.'],
            ['cancellation', 'Free cancellation', 'Free cancellation up to 48 hours before arrival. Late cancellations charge one night.'],
            ['child', 'Children & extra beds', 'Children under 6 stay free when sharing a parent\'s bed. Extra beds available on request.'],
            ['pet', 'Pet policy', 'Pets are not allowed. Service animals are welcome with prior notice.'],
        ];

        $nearbyTemplates = [
            ['name' => 'International Airport', 'place_type' => 'airport', 'distance_km' => 12.4],
            ['name' => 'Central Market', 'place_type' => 'market', 'distance_km' => 1.8],
            ['name' => 'Riverside Promenade', 'place_type' => 'attraction', 'distance_km' => 0.5],
            ['name' => 'National Museum', 'place_type' => 'museum', 'distance_km' => 1.1],
            ['name' => 'Independence Monument', 'place_type' => 'landmark', 'distance_km' => 2.3],
        ];

        foreach ($properties as $i => $property) {
            // amenities pivot ------------------------------------------------
            if ($propertyAmenities->isNotEmpty() && $property->amenities()->count() === 0) {
                $picked = $propertyAmenities->random(min(5, $propertyAmenities->count()))->pluck('id')->all();
                $property->amenities()->sync($picked);
            }

            // photos ---------------------------------------------------------
            if ($property->images()->count() === 0) {
                $photos = collect($photoLibrary)->shuffle()->take(4);
                foreach ($photos as $idx => $url) {
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $url,
                        'title' => $property->name.' photo '.($idx + 1),
                        'is_cover' => $idx === 0,
                        'sort_order' => $idx,
                        'status' => 'active',
                    ]);
                }
            }

            // policies -------------------------------------------------------
            if ($property->policies()->count() === 0) {
                foreach (collect($policyTemplates)->random(min(3, count($policyTemplates))) as $row) {
                    PropertyPolicy::create([
                        'property_id' => $property->id,
                        'policy_type' => $row[0],
                        'title' => $row[1],
                        'description' => $row[2],
                        'status' => 'active',
                    ]);
                }
            }

            // nearby places --------------------------------------------------
            if ($property->nearbyPlaces()->count() === 0) {
                foreach (collect($nearbyTemplates)->random(min(3, count($nearbyTemplates))) as $row) {
                    NearbyPlace::create([
                        'property_id' => $property->id,
                        'name' => $row['name'],
                        'place_type' => $row['place_type'],
                        'distance_km' => $row['distance_km'] + ($i * 0.2),
                        'description' => null,
                    ]);
                }
            }
        }

        // room types --------------------------------------------------------
        $roomTypes = RoomType::orderBy('id')->get();
        foreach ($roomTypes as $idx => $roomType) {
            // amenities pivot
            if ($roomAmenities->isNotEmpty() && $roomType->amenities()->count() === 0) {
                $picked = $roomAmenities->random(min(3, $roomAmenities->count()))->pluck('id')->all();
                $roomType->amenities()->sync($picked);
            }

            // images
            if ($roomType->images()->count() === 0) {
                $url = $roomPhotos[$idx % count($roomPhotos)];
                RoomTypeImage::create([
                    'room_type_id' => $roomType->id,
                    'image_path' => $url,
                    'title' => $roomType->name,
                    'is_cover' => true,
                    'sort_order' => 0,
                ]);
            }

            // physical rooms
            if ($roomType->rooms()->count() === 0) {
                $count = 4;
                for ($n = 1; $n <= $count; $n++) {
                    $floor = (string) (1 + (int) floor(($n - 1) / 2));
                    $roomNumber = $floor.str_pad((string) $n, 2, '0', STR_PAD_LEFT);
                    $status = match ($n % 4) {
                        1 => 'available',
                        2 => 'occupied',
                        3 => 'maintenance',
                        default => 'available',
                    };
                    Room::firstOrCreate(
                        ['property_id' => $roomType->property_id, 'room_number' => $roomNumber],
                        [
                            'room_type_id' => $roomType->id,
                            'floor' => $floor,
                            'status' => $status,
                        ]
                    );
                }
            }
        }
    }
}
