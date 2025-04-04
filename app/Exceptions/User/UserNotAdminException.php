<?php

namespace App\Exceptions\User;

use App\Exceptions\Base\PermissionDeniedException;

class UserNotAdminException extends PermissionDeniedException
{
}