<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Mail\ContactMessageEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessagingController extends Controller
{
    const ERROR_CODES = [
        'EMPTY_DATA' => 'empty_data',
    ];

    public function contactMessage(Request $request): JsonResponse
    {
        $this->validate_ExceptionResponseJsonApi($request, [
            'data.attributes.name' => 'required',
            'data.attributes.email' => 'required|email',
            'data.attributes.message' => 'required',
        ]);

        $data = [
            'name' => $request->input('data.attributes.name'),
            'email' => $request->input('data.attributes.email'),
            'message' => $request->input('data.attributes.message'),
        ];

        $jsonApiResponse = MailHelper::sendEmail($data['email'], new ContactMessageEmail($data));

        return $jsonApiResponse;
    }
}
