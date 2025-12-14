<?php

namespace Tests;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function signIn()
    {
        $user = User::where('email', 'test@example.com')->first();

        // Authenticate as this user
        $this->actingAs($user);

    }
}
