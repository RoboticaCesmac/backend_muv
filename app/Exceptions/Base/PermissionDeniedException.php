<?php

namespace App\Exceptions\Base;

use App\Exceptions\MyException;
use Illuminate\Http\Response;

class PermissionDeniedException extends MyException
{
    protected function defineStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}