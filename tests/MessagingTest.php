<?php

use App\Helpers\HttpStatusCodes;
use App\Helpers\MailHelper;
use App\Mail\ContactMessageMailable;

class MessagingTest extends TestCase
{
    private $data = [ 'data' => [
        'type' => 'contactMessage',
        'attributes' => [
            'name' => 'Test Name',
            'email' => 'valid@email.format',
            'message' => 'Test message.',
        ],
    ]];

    public function setUp()
    {
        parent::setUp();

        config(['mail.driver' => 'log']);
    }

    /* EMAIL HELPER ***********************************************************/

    public function testMailHelper_ErrorInvalidEmail()
    {
        $invalidData = $this->data['data']['attributes'];
        $invalidData['email'] = 'invalid-email';

        $jsonApiResponse = MailHelper::sendEmail($invalidData['email'], new ContactMessageMailable($invalidData));

        $this->assertEquals($jsonApiResponse->status(), HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY);
        $this->assertEquals('email', $jsonApiResponse->getData()->errors[0]->source->parameter);
        $this->assertEquals('Email Error', $jsonApiResponse->getData()->errors[0]->title);
        $this->assertEquals(
            'Address in mailbox given [' . $invalidData['email'] . '] does not comply with RFC 2822, 3.6.2.',
            $jsonApiResponse->getData()->errors[0]->detail
        );
    }

    public function testMailHelper_ErrorWrongMailgunDomain()
    {
        $this->markTestSkipped('Skip: Do not access Mailgun with wrong domain often to avoid being blacklisted.');

        config(['mail.driver' => 'mailgun']);
        config(['services.mailgun.domain' => 'invalid-domain']);

        $jsonApiResponse = MailHelper::sendEmail(
            $this->data['data']['attributes']['email'],
            new ContactMessageEmail($this->data['data']['attributes'])
        );

        $this->assertEquals($jsonApiResponse->status(), HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST);
        $this->assertEquals('email', $jsonApiResponse->getData()->errors[0]->source->parameter);
        $this->assertEquals(
            'The contact message cannot be sent because of an error sending the email.',
            $jsonApiResponse->getData()->errors[0]->title
        );
    }

    public function testMailHelper_ErrorWrongMailgunSecret()
    {
        $this->markTestSkipped('Skip: Do not access the valid domain with wrong key often to avoid being blacklisted.');

        config(['mail.driver' => 'mailgun']);
        config(['services.mailgun.secret' => 'invalid-secret']);

        $jsonApiResponse = MailHelper::sendEmail(
            $this->data['data']['attributes']['email'],
            new ContactMessageEmail($this->data['data']['attributes'])
        );

        $this->assertEquals($jsonApiResponse->status(), HttpStatusCodes::CLIENT_ERROR_BAD_REQUEST);
        $this->assertEquals('email', $jsonApiResponse->getData()->errors[0]->source->parameter);
        $this->assertEquals(
            'The contact message cannot be sent because of an error sending the email.',
            $jsonApiResponse->getData()->errors[0]->title
        );
    }

    /* CONTACT MESSAGE ********************************************************/

    public function testSendContactMessage_ErrorNoData()
    {
        $this->post('/api/contact-message', [])
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure);
    }

    public function testSendContactMessage_ErrorEmptyEmail()
    {
        $invalidData = $this->data;
        $invalidData['data']['attributes']['email'] = '';

        $this->post('/api/contact-message', $invalidData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure);
    }

    public function testSendContactMessage_ErrorInvalidEmail()
    {
        $invalidData = $this->data;
        $invalidData['data']['attributes']['email'] = 'invalid-email';

        $this->post('/api/contact-message', $invalidData)
            ->seeStatusCode(HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure($this->jsonApiErrorStructure);
    }

    public function testSendContactMessage_Success()
    {
//        $this->markTestSkipped('Skip: Do not write too much on log.');

        $this->post('/api/contact-message', $this->data)
            ->seeStatusCode(HttpStatusCodes::SUCCESS_NO_CONTENT);
    }
}
