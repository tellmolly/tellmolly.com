<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function run()
    {
        Category::firstOrCreate([
            'name' => '&#128515;',
            'color' => '#3498DB'
        ], [
            'order' => Category::GREAT
        ]);
        Category::firstOrCreate([
            'name' => '&#128578;',
            'color' => '#2ECC71'
        ], [
            'order' => Category::GOOD
        ]);
        Category::firstOrCreate([
            'name' => '&#128528;',
            'color' => '#F1C40F'
        ], [
            'order' => Category::AVERAGE
        ]);
        Category::firstOrCreate([
            'name' => '&#128577;',
            'color' => '#E67E22'
        ], [
            'order' => Category::BAD
        ]);
        Category::firstOrCreate([
            'name' => '&#128544;',
            'color' => '#E74C3C'
        ], [
            'order' => Category::WORST
        ]);
    }
}
