<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InvalidTokenException extends MyException
{
    protected function defineMessage(): string
    {
        return 'Token inválido';
    }

    protected function defineStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
} 