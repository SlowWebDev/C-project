<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            ['name' => 'Commercial Mall', 'icon' => 'fa-store'],
            ['name' => 'Club House', 'icon' => 'fa-house-flag'],
            ['name' => 'Lagoons', 'icon' => 'fa-water'],
            ['name' => 'Water Features', 'icon' => 'fa-water-ladder'],
            ['name' => 'Running Track', 'icon' => 'fa-person-running'],
            ['name' => 'Parking', 'icon' => 'fa-square-parking'],
            ['name' => 'Security', 'icon' => 'fa-shield-halved'],
            ['name' => 'Kids Area', 'icon' => 'fa-children'],
            ['name' => 'Hypermarket', 'icon' => 'fa-cart-shopping'],
            ['name' => 'Pharmacies', 'icon' => 'fa-prescription-bottle-medical'],
            ['name' => 'Banks', 'icon' => 'fa-building-columns'],
            ['name' => 'Restaurants', 'icon' => 'fa-utensils']        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }
    }
}
