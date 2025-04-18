<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class TokenNotFoundException extends MyException
{
    protected function defineMessage(): string
    {
        return 'Token não encontrado para este email';
    }

    protected function defineStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
} 