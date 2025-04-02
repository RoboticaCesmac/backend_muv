<?php

namespace App\Exceptions;

use Throwable;

class TypeException
{
    /**
     * @var string
     */
    public string $message;

    /**
     * @var string
     */
    public string $code;

    /**
     * @var int
     */
    public int $statusCode;

    /**
     * @var array|null
     */
    public ?array $errors;

    /**
     * @var Throwable
     */
    public Throwable $exception;

    /**
     * Create a new TypeException instance.
     *
     * @param string $message
     * @param Throwable $exception
     * @param int $statusCode
     * @param array|null $errors
     */
    public function __construct(
        string $message,
        Throwable $exception,
        int $statusCode,
        ?array $errors = null
    ) {
        $this->message = $message;
        $this->exception = $exception;
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        $this->code = $this->generateCode();
    }

    /**
     * Generate a unique code for the exception.
     *
     * @return string
     */
    private function generateCode(): string
    {
        $className = get_class($this->exception);
        $parts = explode('\\', $className);
        $exceptionName = end($parts);

        if (preg_match('/^[A-Z][a-z]+(?:[A-Z][a-z]+)*$/', $exceptionName)) {
            $code = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $exceptionName));
            return str_replace('_exception', '', $code);
        }

        return str_replace('exception', '', strtolower($exceptionName));
    }

    /**
     * Convert the exception to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $response = [
            'message' => $this->message,
            'code' => $this->code,
            'status_code' => $this->statusCode,
        ];

        if ($this->errors) {
            $response['errors'] = $this->errors;
        }

        if (config('app.debug')) {
            $response['debug'] = [
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'trace' => $this->exception->getTrace(),
            ];
        }

        return $response;
    }
} 