<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account (change the password after the first login)
        $admin = User::updateOrCreate(
            ['email' => 'admin@dreamhome.test'],
            ['name' => 'Admin', 'password' => 'password', 'is_admin' => true]
        );

        // Locations: city => price per sqm (sample values for demo data only)
        $rates = [
            'Bacoor' => 62000, 'Imus' => 58000, 'Dasmariñas' => 52000,
            'General Trias' => 50000, 'Carmona' => 48000, 'Silang' => 45000,
        ];
        $locations = [];
        foreach ($rates as $city => $rate) {
            $locations[$city] = Location::firstOrCreate(['city' => $city], ['province' => 'Cavite']);
        }

        // Property types: name => price multiplier
        $multipliers = ['House and Lot' => 1.0, 'Townhouse' => 0.9, 'Condominium' => 1.15];
        $types = [];
        foreach ($multipliers as $name => $m) {
            $types[$name] = PropertyType::firstOrCreate(['type_name' => $name]);
        }
        PropertyType::firstOrCreate(['type_name' => 'Lot Only']);

        // Sample listings. The same seed always gives the same data.
        mt_srand(2026);
        $cities = array_keys($rates);
        $typeNames = array_keys($multipliers);

        for ($i = 0; $i < 150; $i++) {
            $city = $cities[mt_rand(0, count($cities) - 1)];
            $typeName = $typeNames[mt_rand(0, count($typeNames) - 1)];
            $bedrooms = mt_rand(1, 5);
            $bathrooms = max(1, min($bedrooms, mt_rand(1, $bedrooms)));
            $floorArea = mt_rand(25, 40) * $bedrooms + mt_rand(10, 30);

            $base = $floorArea * $rates[$city] * $multipliers[$typeName]
                + $bedrooms * 120000 + $bathrooms * 90000;
            $price = round($base * (0.9 + mt_rand(0, 25) / 100), -4);

            Property::create([
                'title' => "{$bedrooms}BR {$typeName} in {$city}",
                'description' => "A {$bedrooms}-bedroom, {$bathrooms}-bathroom {$typeName} with {$floorArea} sqm of floor area in {$city}, Cavite. Close to schools, markets, and public transport.",
                'price' => $price,
                'location_id' => $locations[$city]->id,
                'type_id' => $types[$typeName]->id,
                'bedrooms' => $bedrooms,
                'bathrooms' => $bathrooms,
                'floor_area' => $floorArea,
                'status' => mt_rand(1, 10) === 1 ? 'Unavailable' : 'Available',
                'admin_id' => $admin->id,
            ]);
        }
    }
}
