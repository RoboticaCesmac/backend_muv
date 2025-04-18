<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class TokenExpiredException extends MyException
{
    protected function defineMessage(): string
    {
        return 'Token expirado';
    }

    protected function defineStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
} 