<?php

namespace App\Traits;

use App\Exceptions\TypeException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

trait ApiExceptionHandler
{
    protected function handleApiException(Request $request, \Throwable $e): TypeException
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

            case $e instanceof ModelNotFoundException:
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

            case $e instanceof JWTException:
                return new TypeException(
                    'Token não fornecido ou inválido.',
                    $e,
                    Response::HTTP_UNAUTHORIZED
                );

            default:
                return new TypeException(
                    'Ocorreu um erro inesperado.',
                    $e,
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
        }
    }
} 