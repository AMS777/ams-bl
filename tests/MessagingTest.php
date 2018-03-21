<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Helpers\HttpStatusCodes;

class MessagingTest extends TestCase
{
    private $contactMessage = [ 'data' => [
        'type' => 'contactMessage',
        'attributes' => [
            'name' => 'Test Name',
            'email' => 'test@test.test',
            'message' => 'Test message.',
        ],
    ]];

    public function testSendContactMessage_Success()
    {
        config(['mail.driver' => 'log']);

        $this->post('/contact-message', $this->contactMessage)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_NO_CONTENT);
    }

    public function testSendContactMessage_ErrorNoData()
    {
        config(['mail.driver' => 'log']);

        $this->post('/contact-message', [])
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY);
    }
}
