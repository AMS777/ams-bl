<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Helpers\ResponseHelper;
use \Log;

use \Exception;
use \Swift_TransportException;
use \Swift_RfcComplianceException;

class MailHelper
{

    const ERROR_CODES = [
        'INVALID_EMAIL_ADDRESS' => 'invalid_email_address',
        'SENDMAIL_PROCESS_ERROR' => 'sendmail_process_error',
        'ERROR_SENDING_EMAIL' => 'error_sending_email',
    ];

    public static function sendEmail(
        string $emailTo, $emailContent, JsonResponse $successResponse = null
    ): JsonResponse
    {
        try {
            // Laravel provides a clean, simple API over the popular SwiftMailer library:
            // https://swiftmailer.symfony.com/docs/sending.html
            Mail::to($emailTo)->bcc(env('MAIL_FROM_ADDRESS'))->send($emailContent);

        } catch(Swift_RfcComplianceException $e) {
            Log::error($e->getMessage() . ' [Helpers/MailHelper.php->sendEmail(): catch(Swift_RfcComplianceException)]');

            return ResponseHelper::codeJsonApi_Error(self::ERROR_CODES['INVALID_EMAIL_ADDRESS']);

        } catch(Swift_TransportException $e) {
            Log::error($e->getMessage() . ' [Helpers/MailHelper.php->sendEmail(): catch(Swift_TransportException)]');

            return ResponseHelper::codeJsonApi_Error(self::ERROR_CODES['SENDMAIL_PROCESS_ERROR']);

        } catch(Exception $e) {
            Log::error($e->getMessage() . ' [Helpers/MailHelper.php->sendEmail(): catch(Exception)]');

            return ResponseHelper::codeJsonApi_Error(self::ERROR_CODES['ERROR_SENDING_EMAIL']);
        }

//        if ($successResponse) {
//
//            return $successResponse;
//        }

        return ResponseHelper::getNoContentJsonResponse();
    }
}
