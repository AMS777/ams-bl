<?php

use App\Helpers\HttpStatusCodes;

class UserTest extends TestCase
{
    private $userData = [
        'email' => 'test@test.test',
        'name' => 'Test Name',
    ];

    public function testRegisterUserWithoutPassword()
    {
        $this->notSeeInDatabase('users', $this->userData);

        $this->post('/user', $this->userData)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->seeInDatabase('users', $this->userData);
    }

    public function testGetUserWithoutPassword()
    {
        $this->get('/user?email=' . $this->userData['email'])
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->seeJson($this->userData);
    }

    public function testErrorGetUserEmptyEmail()
    {
        $this->get('/user?email=')
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->assertEmpty($this->response->content());
    }

    public function testDeleteUser()
    {
        $this->seeInDatabase('users', $this->userData);

        $this->delete('/user', ['email' => $this->userData['email']])
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->notSeeInDatabase('users', $this->userData);
    }

}
