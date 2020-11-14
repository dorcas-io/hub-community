<?php

namespace App\Http\Controllers\Ajax\Account;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Account extends Controller
{
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resendVerification(Request $request, Sdk $sdk)
    {
        $user = $request->user();
        # get the authenticated user
        $query = $sdk->createUserResource($user->id)->send('POST', ['resend-verification']);
        # send the request
        if (!$query->isSuccessful()) {
            throw new \RuntimeException($query->errors[0]['title'] ?? 'Could not resend the verification email.');
        }
        return response()->json($query->getData());
    }
}
