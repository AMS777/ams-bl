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

            $errors = ['email' => [$e->getMessage()]];

            return ResponseHelper::getJsonApiErrorResponse($errors, HttpStatusCodes::CLIENT_ERROR_UNPROCESSABLE_ENTITY);

        } catch(Swift_TransportException $e) {
            Log::error($e->getMessage() . ' [Helpers/MailHelper.php->sendEmail(): catch(Swift_TransportException)]');

            $errors = ['email' => ['The contact message cannot be sent because of an error with the sendmail process.']];

            return ResponseHelper::getJsonApiErrorResponse($errors);

        } catch(Exception $e) {
            Log::error($e->getMessage() . ' [Helpers/MailHelper.php->sendEmail(): catch(Exception)]');

            $errors = ['email' => ['The contact message cannot be sent because of an error sending the email.']];

            return ResponseHelper::getJsonApiErrorResponse($errors);
        }

//        if ($successResponse) {
//
//            return $successResponse;
//        }

        return ResponseHelper::getNoContentJsonResponse();
    }
}
