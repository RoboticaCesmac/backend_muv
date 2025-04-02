<?php

namespace App\Exceptions\Base;

use App\Exceptions\MyException;
use Illuminate\Http\Response;

abstract class NotFoundException extends MyException
{
    protected function defineStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}   