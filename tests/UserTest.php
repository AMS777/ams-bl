<?php

use App\Helpers\HttpStatusCodes;

class UserTest extends TestCase
{
    private $userData = [
        'email' => 'test@test.test',
        'name' => 'Test Name',
    ];

    public function testRegisterUser_WithoutPassword()
    {
        $this->notSeeInDatabase('users', $this->userData);

        $this->post('/user', $this->userData)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_CREATED)
            ->seeInDatabase('users', $this->userData);
    }

    public function testRegisterUser_ErrorExistingEmail()
    {
        $this->seeInDatabase('users', $this->userData);

        $this->post('/user', $this->userData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJson(['email' => ['The email "' . $this->userData['email'] . '" is already used.']])
            ->seeInDatabase('users', $this->userData);
    }

    public function testRegisterUser_ErrorInvalidEmail()
    {
        $invalidUserData = [
            'email' => 'invalid.email',
            'name' => 'Test Name',
        ];

        $this->post('/user', $invalidUserData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJson(['email' => ['The email must be a valid email address.']])
            ->notSeeInDatabase('users', $invalidUserData);
    }

    public function testRegisterUser_ErrorEmptyEmail()
    {
        $invalidUserData = [
            'email' => '',
            'name' => 'Test Name',
        ];

        $this->post('/user', $invalidUserData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJson(['email' => ['The email field is required.']])
            ->notSeeInDatabase('users', $invalidUserData);
    }

    public function testGetUser_WithoutPassword()
    {
        $this->get('/user?email=' . $this->userData['email'])
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->seeJson($this->userData);
    }

    public function testGetUser_ErrorInvalidEmail()
    {
        $this->get('/user?email=invalid.email')
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJson(['email' => ['The email must be a valid email address.']]);
    }

    public function testGetUser_ErrorEmailDoesNotExist()
    {
        $notExistingEmail = 'not.existing.email@test.test';

        $this->get('/user?email=' . $notExistingEmail)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJson(['email' => ['The email "' . $notExistingEmail . '" does not exist.']]);
    }

    public function testGetUser_ErrorEmptyEmail()
    {
        $this->get('/user?email=')
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJson(['email' => ['The email field is required.']]);
    }

    public function testDeleteUser()
    {
        $this->seeInDatabase('users', $this->userData);

        $this->delete('/user', ['email' => $this->userData['email']])
            ->seeStatusCode(HttpStatusCodes::SUCCESS_NO_CONTENT)
            ->notSeeInDatabase('users', $this->userData);
    }

}
