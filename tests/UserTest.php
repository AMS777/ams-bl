<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    public function testGetUserWithoutPassword()
    {
        $this->get('/user', ['email' => 'test@test.test'])
            ->seeJson([
                'email' => 'test@test.test',
                'name' => 'Test Name',
            ]);
    }
}
