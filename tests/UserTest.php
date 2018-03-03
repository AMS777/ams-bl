<?php

use App\Helpers\HttpStatusCodes;

class UserTest extends TestCase
{
    private $userData = [
        'email' => 'test@test.test',
        'name' => 'Test Name',
    ];
    private $jsonApiStructure = [
        'jsonapi',
        'data' => ['type', 'id', 'attributes'],
    ];
    private $jsonApiTypeUser = ['type' => 'user'];
    private $jsonApiErrorStructure = [
        'jsonapi',
        'errors' => [['source' => ['parameter'], 'title']],
    ];

    public function testRegisterUser_WithoutPassword()
    {
        $this->notSeeInDatabase('users', $this->userData);

        $this->post('/user', $this->userData)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_CREATED)
            ->seeJsonStructure($this->jsonApiStructure)
            ->seeJson($this->jsonApiTypeUser)
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
            ->seeJsonStructure($this->jsonApiStructure)
            ->seeJson($this->jsonApiTypeUser)
            ->seeJson($this->userData);
    }

    public function testGetUser_ErrorEmailGeneral()
    {
        $this->get('/user?email=invalid.email')
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure)
            ->seeJson(['parameter' => 'email']);
    }

    public function testGetUser_ErrorInvalidEmail()
    {
        $this->get('/user?email=invalid.email')
            ->seeJson(['title' => 'The email must be a valid email address.']);
    }

    public function testGetUser_ErrorEmailDoesNotExist()
    {
        $notExistingEmail = 'not.existing.email@test.test';

        $this->get('/user?email=' . $notExistingEmail)
            ->seeJson(['title' => 'The email "' . $notExistingEmail . '" does not exist.']);
    }

    public function testGetUser_ErrorEmptyEmail()
    {
        $this->get('/user?email=')
            ->seeJson(['title' => 'The email field is required.']);
    }

    public function testDeleteUser()
    {
        $this->seeInDatabase('users', $this->userData);

        $this->delete('/user', ['email' => $this->userData['email']])
            ->seeStatusCode(HttpStatusCodes::SUCCESS_NO_CONTENT)
            ->notSeeInDatabase('users', $this->userData);
    }

}
