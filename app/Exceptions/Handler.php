<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Configuration\Exceptions;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Traits\ApiExceptionHandler;
use App\Traits\InertiaExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiExceptionHandler, InertiaExceptionHandler;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        MyException::class,
        ValidationException::class,
        AuthorizationException::class,
        ModelNotFoundException::class,
        JWTException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Configura os handlers de exceção da aplicação
     */
    public static function config(Exceptions $exceptions): void
    {
        $exceptions->render(function (Throwable $e, Request $request) {
            return (new static(app()))->render($request, $e);
        });
    }

    /**
     * Handle the exception and return a TypeException.
     *
     * @param Request $request
     * @param Throwable $e
     * @return TypeException
     */
    protected function handleException(Request $request, Throwable $e): TypeException
    {
        switch (true) {
            case $e instanceof ValidationException:
                return new TypeException(
                    'Erro na validação de campo.',
                    $e,
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $e->errors()
                );

            case $e instanceof AuthorizationException:
                return new TypeException(
                    'Esta ação não é autorizada.',
                    $e,
                    Response::HTTP_FORBIDDEN
                );

            case $e instanceof ModelNotFoundException && $request->expectsJson():
                $model = $e->getModel();
                $id = implode(', ', (array) $e->getIds());
                return new TypeException(
                    "Nenhum resultado de consulta para o modelo [$model] $id",
                    $e,
                    Response::HTTP_NOT_FOUND
                );

            case $e instanceof QueryException:
                $errorsCode = [
                    '1400' => 'Não será possível continuar com a inclusão, existe campo que não pode ser vazio.',
                    '1407' => 'Não será possível continuar com a atualização, existe campo que não pode ser vazio.',
                    '2292' => 'Não será possível continuar com a exclusão, ela é referenciada por outros registros no banco de dados.',
                ];
                $errorCodeUnregistered = 'Não foi possível executar a instrução no banco de dados.';
                $message = $errorsCode[$e->getCode()] ?? $errorCodeUnregistered;
                return new TypeException($message, $e, Response::HTTP_NOT_ACCEPTABLE);

            case $e instanceof MyException:
                return new TypeException($e->getMessage(), $e, $e->getStatusCode());

            case $e instanceof JWTException:
                return new TypeException(
                    'Token de autorização não encontrado',
                    $e,
                    Response::HTTP_UNAUTHORIZED
                );

            case $e instanceof DecryptException:
                return new TypeException(
                    'Você não tem permissão para acessar este recurso.',
                    $e,
                    Response::HTTP_FORBIDDEN
                );

            default:
                return new TypeException(
                    "[{$e->getCode()}] - {$e->getMessage()}",
                    $e,
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if ($request->is('api/*')) {
            $typeException = $this->handleApiException($request, $e);
            return response()->json($typeException->toArray(), $typeException->statusCode);
        }

        if ($request->header('X-Inertia')) {
            return $this->handleInertiaException($request, $e);
        }

        return parent::render($request, $e);
    }
} 