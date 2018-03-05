<?php

use App\Helpers\HttpStatusCodes;
use App\Models\UserModel;

class UserTest extends TestCase
{
    private $userData = [
        'email' => 'test@test.test',
        'name' => 'Test Name',
        'password' => 'test_password.#',
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

    /* REGISTER USER **********************************************************/

    public function testRegisterUser_ErrorEmailGeneral()
    {
        $invalidUserData = $this->userData;
        $invalidUserData['email'] = 'invalid.email';

        $this->post('/user', $invalidUserData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure)
            ->seeJson(['parameter' => 'email']);
    }

    public function testRegisterUser_ErrorEmptyEmail()
    {
        $invalidUserData = $this->userData;
        $invalidUserData['email'] = '';

        $this->post('/user', $invalidUserData)
            ->seeJson(['title' => 'The email field is required.'])
            ->notSeeInDatabase('users', $invalidUserData);
    }

    public function testRegisterUser_ErrorInvalidEmail()
    {
        $invalidUserData = $this->userData;
        $invalidUserData['email'] = 'invalid.email';

        $this->post('/user', $invalidUserData)
            ->seeJson(['title' => 'The email must be a valid email address.'])
            ->notSeeInDatabase('users', $invalidUserData);
    }

    public function testRegisterUser_ErrorPasswordTooShort()
    {
        $shortPassword = '1234';

        $invalidUserData = $this->userData;
        $invalidUserData['password'] = $shortPassword;

        $errorMessage = 'The password must be between ' . env('PASSWORD_MIN_CHARACTERS')
            . ' and ' . env('PASSWORD_MAX_CHARACTERS') . ' characters.';

        $this->post('/user', $invalidUserData)
            ->seeJson(['title' => $errorMessage])
            ->notSeeInDatabase('users', $invalidUserData);
    }

    public function testRegisterUser_ErrorPasswordTooLong()
    {
        $longPassword = '';
        for ($i = 0; $i <= 11; $i++) {
            $longPassword .= '0123456789';
        }

        $invalidUserData = $this->userData;
        $invalidUserData['password'] = $longPassword;

        $errorMessage = 'The password must be between ' . env('PASSWORD_MIN_CHARACTERS')
            . ' and ' . env('PASSWORD_MAX_CHARACTERS') . ' characters.';

        $this->post('/user', $invalidUserData)
            ->seeJson(['title' => $errorMessage])
            ->notSeeInDatabase('users', $invalidUserData);
    }

    public function testRegisterUser_Success()
    {
//        $this->notSeeInDatabase('users', $this->userData);

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
            ->seeJson(['title' => 'The email "' . $this->userData['email'] . '" is already used.'])
            ->seeInDatabase('users', $this->userData);
    }

    /* GET USER ***************************************************************/

    public function testPasswordNotReturnedFromDbOnUserModel()
    {
        $user = UserModel::where('email', $this->userData['email'])->first();

        $this->assertNotContains('password', $user->toArray());
        $this->assertArrayNotHasKey('password', $user->toArray());
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

    public function testGetUser_Success()
    {
        $urlQuery = '?email=' . urlencode($this->userData['email'])
            . '&password=' . urlencode($this->userData['password']);

        $userDataWithoutPassword = $this->userData;
        unset($userDataWithoutPassword['password']);

        $this->get('/user' . $urlQuery)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->seeJsonStructure($this->jsonApiStructure)
            ->seeJson($this->jsonApiTypeUser)
            ->seeJson($userDataWithoutPassword);
    }

    /* DELETE USER ************************************************************/

    public function testDeleteUser_Success()
    {
        $this->seeInDatabase('users', $this->userData);

        $this->delete('/user', ['email' => $this->userData['email']])
            ->seeStatusCode(HttpStatusCodes::SUCCESS_NO_CONTENT)
            ->notSeeInDatabase('users', $this->userData);
    }
}
