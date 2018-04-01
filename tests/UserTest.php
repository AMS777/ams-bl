<?php

use App\Helpers\HttpStatusCodes;
use App\Models\UserModel;

class UserTest extends TestCase
{
    private $userDataWithPassword = [ 'data' => [
        'type' => 'user',
        'attributes' => [
            'name' => 'Test Name',
            'email' => 'valid@email.format',
            'password' => 'Test_Password.#áÉíÖüñÑ',
        ],
    ]];
    private $userDataWithoutPassword = [ 'data' => [
        'type' => 'user',
        'attributes' => [
            'name' => 'Test Name',
            'email' => 'valid@email.format',
        ],
    ]];
    private $jsonApiTypeUser = ['type' => 'user'];
    private $oauth2TokenRequest = [
        'grant_type' => 'password',
        'username' => 'valid@email.format',
        'password' => 'Test_Password.#áÉíÖüñÑ',
    ];
    private $oauth2Structure = ['access_token', 'userId', 'userName'];
    private $oauth2ErrorStructure = ['error', 'error_title', 'error_description'];
    private $oldJwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2FwaVwvZ2V0LXRva2VuIiwiaWF0IjoxNTIyNDk3NTIzLCJleHAiOjE1MjI1MDExMjMsIm5iZiI6MTUyMjQ5NzUyMywianRpIjoidmdTNGZXU3hUR2FFem5LQyIsInN1YiI6MzI5LCJwcnYiOiI0MWRmODgzNGYxYjk4ZjcwZWZhNjBhYWVkZWY0MjM0MTM3MDA2OTBjIn0.1FeDFn03i4mmT7cRIU8jy8fylOtBbmfPdATgNq5piG0';

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

        $userId = $this->response->getOriginalContent()->jsonSerialize()['data']['id'];

        return $userId;
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

    /* USER TOKEN *************************************************************/

    public function testGetUserToken_ErrorEmailGeneral()
    {
        $invalidOauth2TokenRequest = $this->oauth2TokenRequest;
        $invalidOauth2TokenRequest['username'] = 'invalid.email';

        $this->post('/api/get-token', $invalidOauth2TokenRequest)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure)
            ->seeJson(['parameter' => 'email'])
            ->seeJson(['title' => 'Email Error']);
    }

    public function testGetUser_ErrorInvalidEmail()
    {
        $invalidOauth2TokenRequest = $this->oauth2TokenRequest;
        $invalidOauth2TokenRequest['username'] = 'invalid.email';

        $this->post('/api/get-token', $invalidOauth2TokenRequest)
            ->seeJson(['detail' => 'The email must be a valid email address.']);
    }

    public function testGetUser_ErrorEmailDoesNotExist()
    {
        $invalidOauth2TokenRequest = $this->oauth2TokenRequest;
        $invalidOauth2TokenRequest['username'] = 'not.existing.email@test.test';

        $this->post('/api/get-token', $invalidOauth2TokenRequest)
            ->seeJson(['detail' => 'The email "' . $invalidOauth2TokenRequest['username'] . '" does not exist.']);
    }

    public function testGetUserToken_ErrorEmptyEmail()
    {
        $invalidOauth2TokenRequest = $this->oauth2TokenRequest;
        $invalidOauth2TokenRequest['username'] = '';

        $this->post('/api/get-token', $invalidOauth2TokenRequest)
            ->seeJson(['detail' => 'The email field is required.']);
    }

    public function testGetUserToken_ErrorWrongPassword()
    {
        $invalidOauth2TokenRequest = $this->oauth2TokenRequest;
        $invalidOauth2TokenRequest['password'] = 'Wrong.Password';

        $this->post('/api/get-token', $invalidOauth2TokenRequest)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNAUTHORIZED)
            ->seeJsonStructure($this->oauth2ErrorStructure)
            ->seeJson(['error' => 'invalid_client'])
            ->seeJson(['error_title' => 'Authentication Error'])
            ->seeJson(['error_description' => 'There is no account with those email and password.']);
    }

    public function testGetUserToken_Success()
    {
        $this->post('/api/get-token', $this->oauth2TokenRequest)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->seeJsonStructure($this->oauth2Structure);

        $accessToken = $this->response->getOriginalContent()['access_token'];

        $this->assertContains(
            '"sub":',
            base64_decode($accessToken),
            'JWT contains reference to user id ("sub", subject): ' . base64_decode($accessToken),
            true
        );
        $this->assertNotContains(
            $this->userDataWithPassword['data']['attributes']['password'],
            base64_decode($accessToken),
            'JWT does not contain user password: ' . base64_decode($accessToken),
            true
        );

        $authHeader = ['Authorization' => 'Bearer ' . $accessToken];

        return $authHeader;
    }

    /* GET USER ***************************************************************/

    /**
     * @depends testRegisterUser_Success
     */
    public function testPasswordNotReturnedFromDbOnUserModel($userId)
    {
        $user = UserModel::where('id', $userId)->first();

        $this->assertNotContains('password', $user->toArray());
        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    /**
     * @depends testRegisterUser_Success
     */
    public function testGetUser_ErrorNoToken($userId)
    {
        $this->get('/api/users/' . $userId)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNAUTHORIZED)
            ->seeJsonStructure($this->jsonApiErrorStructure)
            ->seeJson(['parameter' => 'authentication'])
            ->seeJson(['title' => 'Authentication Error'])
            ->seeJson(['detail' => 'The user is not authenticated.']);
    }

    /**
     * @depends testRegisterUser_Success
     */
    public function testGetUser_ErrorWrongToken($userId)
    {
        $wrongAuthHeader = ['Authorization' => 'Bearer ' . $this->oldJwtToken];

        $this->get('/api/users/' . $userId, $wrongAuthHeader)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNAUTHORIZED)
            ->seeJsonStructure($this->jsonApiErrorStructure)
            ->seeJson(['parameter' => 'authentication'])
            ->seeJson(['title' => 'Authentication Error'])
            ->seeJson(['detail' => 'The user is not authenticated.']);
    }

    /**
     * @depends testRegisterUser_Success
     * @depends testGetUserToken_Success
     */
    public function testGetUser_Success($userId, $authHeader)
    {
        $this->get('/api/users/' . $userId, $authHeader)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_OK)
            ->seeJsonStructure($this->jsonApiStructure)
            ->seeJson($this->jsonApiTypeUser)
            ->seeJson($this->userDataWithoutPassword['data']['attributes']);
    }

    /* DELETE USER ************************************************************/

    /**
     * @depends testGetUserToken_Success
     */
    public function testDeleteUser_Success($authHeader)
    {
        $this->seeInDatabase('users', $this->userDataWithoutPassword['data']['attributes']);

        $params = ['email' => $this->userDataWithoutPassword['data']['attributes']['email']];
        $this->delete('/api/users', $params, $authHeader)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_NO_CONTENT)
            ->notSeeInDatabase('users', $this->userDataWithoutPassword['data']['attributes']);
    }
}
