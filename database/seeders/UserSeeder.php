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
            ->has(Asset::factory()->count(1))
            ->create([
                'name' => 'Johnny Bravo',
                'email' => 'test@example.com',
                'balance' => 36
            ]);

        // other users
        User::factory()
            ->has(Asset::factory()->count(1))
            ->count(5)
            ->create();
    }
}
