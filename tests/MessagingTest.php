<?php

use App\Helpers\HttpStatusCodes;
use App\Helpers\MailHelper;
use App\Mail\ContactMessageEmail;

class MessagingTest extends TestCase
{
    private $data = [ 'data' => [
        'type' => 'contactMessage',
        'attributes' => [
            'name' => 'Test Name',
            'email' => 'test@test.test',
            'message' => 'Test message.',
        ],
    ]];
    private $jsonApiErrorStructure = [
        'jsonapi',
        'errors' => [['source' => ['parameter'], 'title']],
    ];

    public function setUp()
    {
        parent::setUp();

        config(['mail.driver' => 'log']);
    }

    public function testSendContactMessage_ErrorNoData()
    {
        $this->post('/contact-message', [])
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure);
    }

    public function testSendContactMessage_ErrorEmptyEmail()
    {
        $invalidData = $this->data;
        $invalidData['data']['attributes']['email'] = '';

        $this->post('/contact-message', $invalidData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure);
    }

    public function testSendContactMessage_ErrorInvalidEmail()
    {
        $invalidData = $this->data;
        $invalidData['data']['attributes']['email'] = 'invalid-email';

        $this->post('/contact-message', $invalidData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure);
    }

    public function testSendContactMessage_Success()
    {
        $this->markTestSkipped('Skip');
        $this->post('/contact-message', $this->data)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_NO_CONTENT);
    }

    public function testMailHelper_ErrorInvalidEmail()
    {
        $invalidData = $this->data['data']['attributes'];
        $invalidData['email'] = 'invalid-email';

        $jsonApiResponse = MailHelper::sendEmail($invalidData['email'], new ContactMessageEmail($invalidData));

        $this->assertEquals($jsonApiResponse->status(), HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY);
        $this->assertEquals('email', $jsonApiResponse->getData()->errors[0]->source->parameter);
        $this->assertEquals(
            'Address in mailbox given [' . $invalidData['email'] . '] does not comply with RFC 2822, 3.6.2.',
            $jsonApiResponse->getData()->errors[0]->title
        );
    }
}
