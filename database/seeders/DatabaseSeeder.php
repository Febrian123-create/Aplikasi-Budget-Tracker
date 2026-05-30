<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    

    public function run(): void
    {
        if (\DB::table('membership')->count() === 0) {
            \DB::table('membership')->insert([
                ['membership_id' => 1, 'membership_name' => 'Free', 'price' => 0],
                ['membership_id' => 2, 'membership_name' => 'Premium', 'price' => 99000],
            ]);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
