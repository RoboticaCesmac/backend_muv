<?php

namespace App\Exceptions\Base;

use App\Exceptions\MyException;
use Exception;
use Illuminate\Http\Response;

class ConflictException extends MyException
{
    protected function defineStatusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }
}