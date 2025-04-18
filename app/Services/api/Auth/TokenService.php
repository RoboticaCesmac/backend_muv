<?php

namespace App\Services\api\Auth;

use App\Exceptions\InvalidTokenException;
use App\Exceptions\TokenExpiredException;
use App\Exceptions\TokenNotFoundException;
use App\Mail\api\TokenMail;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class TokenService
{
    public function sendRegisterToken(Request $request): Token
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

    public function sendResetPasswordToken(Request $request): Token
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

    /**
     * Confirma se um token é válido para um determinado email
     * Lança exceções em caso de erros
     *
     * @param string $email
     * @param string $token
     * @throws TokenNotFoundException
     * @throws TokenExpiredException
     * @throws InvalidTokenException
     */
    public function confirmResetPasswordToken(array $data): void
    {
        $tokenRecord = Token::where('email', $data['email'])->first();

        if (!$tokenRecord) {
            throw new TokenNotFoundException();
        }

        if (Carbon::parse($tokenRecord->expires_at)->isPast()) {
            throw new TokenExpiredException();
        }

        $storedToken = Crypt::decryptString($tokenRecord->token);

        if ($storedToken !== $data['token']) {
            throw new InvalidTokenException();
        }
    }
}
