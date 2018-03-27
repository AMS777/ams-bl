<?php

use App\Helpers\HttpStatusCodes;
use App\Models\UserModel;

class UserTest extends TestCase
{
    private $userDataWithPassword = [ 'data' => [
        'type' => 'user',
        'attributes' => [
            'name' => 'Test Name',
            'email' => 'test@test.test',
            'password' => 'Test_Password.#áÉíÖüñÑ',
        ],
    ]];
    private $userDataWithoutPassword = [ 'data' => [
        'type' => 'user',
        'attributes' => [
            'name' => 'Test Name',
            'email' => 'test@test.test',
        ],
    ]];
    private $jsonApiTypeUser = ['type' => 'user'];

    /* REGISTER USER **********************************************************/

    public function testRegisterUser_ErrorEmailGeneral()
    {
        $invalidUserData = $this->userDataWithPassword;
        $invalidUserData['data']['attributes']['email'] = 'invalid.email';

        $this->post('/api/users', $invalidUserData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure)
            ->seeJson(['parameter' => 'email'])
            ->seeJson(['title' => 'Email Error']);
    }

    public function testRegisterUser_ErrorEmptyEmail()
    {
        $invalidUserData = $this->userDataWithPassword;
        $invalidUserData['data']['attributes']['email'] = '';

        $this->post('/api/users', $invalidUserData)
            ->seeJson(['detail' => 'The email field is required.'])
            ->notSeeInDatabase('users', $invalidUserData['data']['attributes']);
    }

    public function testRegisterUser_ErrorInvalidEmail()
    {
        $invalidUserData = $this->userDataWithPassword;
        $invalidUserData['data']['attributes']['email'] = 'invalid.email';

        $this->post('/api/users', $invalidUserData)
            ->seeJson(['detail' => 'The email must be a valid email address.'])
            ->notSeeInDatabase('users', $invalidUserData['data']['attributes']);
    }

    public function testRegisterUser_ErrorPasswordTooShort()
    {
        $shortPassword = '1234';

        $invalidUserData = $this->userDataWithPassword;
        $invalidUserData['data']['attributes']['password'] = $shortPassword;

        $errorMessage = 'The password must be between ' . env('PASSWORD_MIN_CHARACTERS')
            . ' and ' . env('PASSWORD_MAX_CHARACTERS') . ' characters.';

        $this->post('/api/users', $invalidUserData)
            ->seeJson(['title' => 'Password Error'])
            ->seeJson(['detail' => $errorMessage])
            ->notSeeInDatabase('users', $invalidUserData['data']['attributes']);
    }

    public function testRegisterUser_ErrorPasswordTooLong()
    {
        $longPassword = '';
        for ($i = 0; $i <= 11; $i++) {
            $longPassword .= '0123456789';
        }

        $invalidUserData = $this->userDataWithPassword;
        $invalidUserData['data']['attributes']['password'] = $longPassword;

        $errorMessage = 'The password must be between ' . env('PASSWORD_MIN_CHARACTERS')
            . ' and ' . env('PASSWORD_MAX_CHARACTERS') . ' characters.';

        $this->post('/api/users', $invalidUserData)
            ->seeJson(['title' => 'Password Error'])
            ->seeJson(['detail' => $errorMessage])
            ->notSeeInDatabase('users', $invalidUserData['data']['attributes']);
    }

    public function testRegisterUser_Success()
    {
        $this->notSeeInDatabase('users', $this->userDataWithoutPassword['data']['attributes']);

        $this->post('/api/users', $this->userDataWithPassword)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_CREATED)
            ->seeJsonStructure($this->jsonApiStructure)
            ->seeJson($this->jsonApiTypeUser)
            ->notSeeInDatabase('users', $this->userDataWithPassword['data']['attributes'])
            ->seeInDatabase('users', $this->userDataWithoutPassword['data']['attributes']);
    }

    /**
     * @depends testRegisterUser_Success
     */
    public function testRegisterUser_ErrorExistingEmail()
    {
        $this->seeInDatabase('users', $this->userDataWithoutPassword['data']['attributes']);

        $this->post('/api/users', $this->userDataWithPassword)
            ->seeJson([
                'detail' => 'The email "' . $this->userDataWithoutPassword['data']['attributes']['email'] . '" is already used.'
            ])
            ->seeInDatabase('users', $this->userDataWithoutPassword['data']['attributes']);
    }

    /* GET USER ***************************************************************/

    /**
     * @depends testRegisterUser_Success
     */
    public function testPasswordNotReturnedFromDbOnUserModel()
    {
        $user = UserModel::where('email', $this->userDataWithPassword['data']['attributes']['email'])->first();

        $this->assertNotContains('password', $user->toArray());
        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    public function testGetUser_ErrorEmailGeneral()
    {
        $this->get('/api/users?email=invalid.email')
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure)
            ->seeJson(['parameter' => 'email'])
            ->seeJson(['title' => 'Email Error']);
    }

    public function testGetUser_ErrorInvalidEmail()
    {
        $this->get('/api/users?email=invalid.email')
            ->seeJson(['detail' => 'The email must be a valid email address.']);
    }

    public function testGetUser_ErrorEmailDoesNotExist()
    {
        $notExistingEmail = 'not.existing.email@test.test';

        $this->get('/api/users?email=' . $notExistingEmail)
            ->seeJson(['detail' => 'The email "' . $notExistingEmail . '" does not exist.']);
    }

    public function testGetUser_ErrorEmptyEmail()
    {
        $this->get('/api/users?email=')
            ->seeJson(['detail' => 'The email field is required.']);
    }

    /**
     * @depends testRegisterUser_Success
     */
    public function testGetUser_ErrorWrongPassword()
    {
        $urlQuery = '?email=' . urlencode($this->userDataWithPassword['data']['attributes']['email'])
            . '&password=WRONG_PASSWORD';

        $this->get('/api/users' . $urlQuery)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure)
            ->seeJson(['title' => 'Email, Password Error'])
            ->seeJson(['detail' => 'There is no account with those email and password.']);
    }

    /**
     * @depends testRegisterUser_Success
     */
    public function testGetUser_Success()
    {
        $urlQuery = '?email=' . urlencode($this->userDataWithPassword['data']['attributes']['email'])
            . '&password=' . urlencode($this->userDataWithPassword['data']['attributes']['password']);

        $this->get('/api/users' . $urlQuery)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->seeJsonStructure($this->jsonApiStructure)
            ->seeJson($this->jsonApiTypeUser)
            ->seeJson($this->userDataWithoutPassword['data']['attributes']);
    }

    /**
     * @depends testRegisterUser_Success
     */
    public function testGetUserToken_Success()
    {
        $oauth2TokenRequest = [
            'grant_type' => 'password',
            'username' => $this->userDataWithPassword['data']['attributes']['email'],
            'password' => $this->userDataWithPassword['data']['attributes']['password'],
        ];
        $this->post('/api/get-token', $oauth2TokenRequest)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->seeJsonStructure(['access_token']);
    }

    /* DELETE USER ************************************************************/

    public function testDeleteUser_Success()
    {
        $this->seeInDatabase('users', $this->userDataWithoutPassword['data']['attributes']);

        $this->delete('/api/users', ['email' => $this->userDataWithoutPassword['data']['attributes']['email']])
            ->seeStatusCode(HttpStatusCodes::SUCCESS_NO_CONTENT)
            ->notSeeInDatabase('users', $this->userDataWithoutPassword['data']['attributes']);
    }
}
