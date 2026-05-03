<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bangladesh university campuses
        $campuses = [
            ['name' => 'Bangladesh University of Engineering and Technology', 'slug' => 'buet', 'short_name' => 'BUET', 'city' => 'Dhaka', 'lat' => 23.7261, 'lng' => 90.3927, 'color_primary' => '#1d4ed8', 'color_secondary' => '#3b82f6'],
            ['name' => 'University of Dhaka', 'slug' => 'du', 'short_name' => 'DU', 'city' => 'Dhaka', 'lat' => 23.7280, 'lng' => 90.3985, 'color_primary' => '#15803d', 'color_secondary' => '#22c55e'],
            ['name' => 'North South University', 'slug' => 'nsu', 'short_name' => 'NSU', 'city' => 'Dhaka', 'lat' => 23.8153, 'lng' => 90.4244, 'color_primary' => '#7c3aed', 'color_secondary' => '#a78bfa'],
            ['name' => 'BRAC University', 'slug' => 'bracu', 'short_name' => 'BRACU', 'city' => 'Dhaka', 'lat' => 23.7808, 'lng' => 90.4093, 'color_primary' => '#b45309', 'color_secondary' => '#f59e0b'],
            ['name' => 'Independent University Bangladesh', 'slug' => 'iub', 'short_name' => 'IUB', 'city' => 'Dhaka', 'lat' => 23.8230, 'lng' => 90.4200, 'color_primary' => '#be123c', 'color_secondary' => '#f43f5e'],
            ['name' => 'Chittagong University of Engineering and Technology', 'slug' => 'cuet', 'short_name' => 'CUET', 'city' => 'Chittagong', 'lat' => 22.4607, 'lng' => 91.9718, 'color_primary' => '#0e7490', 'color_secondary' => '#06b6d4'],
            ['name' => 'Rajshahi University', 'slug' => 'ru', 'short_name' => 'RU', 'city' => 'Rajshahi', 'lat' => 24.3745, 'lng' => 88.6042, 'color_primary' => '#9a3412', 'color_secondary' => '#ea580c'],
            ['name' => 'Shahjalal University of Science and Technology', 'slug' => 'sust', 'short_name' => 'SUST', 'city' => 'Sylhet', 'lat' => 24.9148, 'lng' => 91.8349, 'color_primary' => '#166534', 'color_secondary' => '#16a34a'],
        ];

        foreach ($campuses as $campus) {
            Campus::firstOrCreate(['slug' => $campus['slug']], $campus);
        }

        $this->command->info('Campuses seeded.');
    }
}
