<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Mail\ContactMessageEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessagingController extends Controller
{
    public function contactMessage(Request $request): JsonResponse
    {
        $this->validate_ExceptionResponseJsonApi($request, [
            'data.attributes.name' => 'required',
            'data.attributes.email' => 'required|email',
            'data.attributes.message' => 'required',
        ]);

        $jsonApiResponse = MailHelper::sendEmail(
            $request->input('data.attributes.email'),
            new ContactMessageEmail($request->input('data.attributes'))
        );

        return $jsonApiResponse;
    }
}
