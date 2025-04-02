<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

abstract class MyException extends Exception
{
    /**
     * @var int
     */
    protected int $statusCode;

    /**
     * Create a new MyException instance.
     *
     * @param string $message
     * @param int $statusCode
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $this->defineStatusCode();
    }

    /**
     * Get the status code for the exception.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    abstract protected function defineStatusCode(): int;
} 