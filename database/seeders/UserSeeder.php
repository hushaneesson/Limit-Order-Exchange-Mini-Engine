<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // default test user
        User::factory()
            ->has(Asset::factory()->count(3))
            ->create([
                'name' => 'Johnny Bravo',
                'email' => 'test@example.com',
            ]);

        // other users
        User::factory()
            ->has(Asset::factory()->count(5))
            ->count(5)
            ->create();
    }
}
