<?php

namespace Tests;

use App\Role;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Create an admin user.
     *
     * @param array $overrides
     * @return User
     */
    public function makeAdminUser(array $overrides = [])
    {
        return factory(User::class)->create($overrides + [
                'role_id' => Role::where('name', 'Admin')->first()->id,
            ]);
    }

    /**
     * Create a regular user.
     *
     * @param array $overrides
     * @return User
     */
    public function makeUser(array $overrides = [])
    {
        return factory(User::class)->create($overrides + [
                'role_id' => Role::where('name', 'User')->first()->id,
            ]);
    }
}
