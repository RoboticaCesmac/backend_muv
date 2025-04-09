<?php

namespace App\Services\api\Auth;

use App\Mail\api\TokenMail;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class TokenService
{
    public function sendToken(Request $request): Token
    {
        $token = Str::padLeft(random_int(0, 999999), 6, '0');

        $tokenObject = Token::updateOrCreate([
            'email' => $request->get('email'),
        ], [
            'token' => Crypt::encryptString($token),
            'expires_at' => now()->addHours(),
        ]);

        Mail::to($request->get('email'))
            ->send(new TokenMail(
                token: $token,
                name: $request->get('email'),
                expiresInMinutes: 60
            ));
        
        return $tokenObject;
    }
}