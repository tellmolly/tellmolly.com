<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function run(): void
    {
        Category::firstOrCreate([
            'order' => Category::GREAT
        ], [
            'name' => '&#128515;',
            'color' => '#3498DB'
        ]);
        Category::firstOrCreate([
            'order' => Category::GOOD
        ], [
            'name' => '&#128578;',
            'color' => '#2ECC71'
        ]);
        Category::firstOrCreate([
            'order' => Category::AVERAGE
        ], [
            'name' => '&#128528;',
            'color' => '#F1C40F'
        ]);
        Category::firstOrCreate([
            'order' => Category::BAD
        ], [
            'name' => '&#128577;',
            'color' => '#E67E22'
        ]);
        Category::firstOrCreate([
            'order' => Category::WORST
        ], [
            'name' => '&#128544;',
            'color' => '#E74C3C'
        ]);
    }
}
