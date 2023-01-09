<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $demoUser = User::firstOrCreate([
            'email' => config('calendar.demo.email')
        ], [
            'name' => 'Demo User',
            'password' => Hash::make(Str::random(10)),
            'email_verified_at' => Carbon::now()
        ]);

        $tags = [
            ['name' => 'Concert', 'color' => '#007a37'],
            ['name' => 'Gym', 'color' => '#ff0000'],
            ['name' => 'Cycling', 'color' => '#2465bf'],
        ];
        foreach ($tags as $tag) {
            $demoUser->tags()->create($tag);
        }
    }
}
