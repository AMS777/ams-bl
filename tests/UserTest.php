<?php

class UserTest extends TestCase
{
    private $userData = [
        'email' => 'test@test.test',
        'name' => 'Test Name',
    ];

    public function testGetUserWithoutPassword()
    {
        $this->get('/user', ['email' => $this->userData['email']])
            ->seeJson($this->userData);
    }

    public function testRegisterUserWithoutPassword()
    {
        $this->notSeeInDatabase('users', $this->userData);

        $this->post('/user', $this->userData)
            ->seeStatusCode(200)
            ->seeInDatabase('users', $this->userData);
    }

    public function testDeleteUser()
    {
        $this->seeInDatabase('users', $this->userData);

        $this->delete('/user', ['email' => $this->userData['email']])
            ->seeStatusCode(200)
            ->notSeeInDatabase('users', $this->userData);
    }

}
