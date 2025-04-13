<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Exceptions\JWTException;

trait InertiaExceptionHandler
{
    protected function handleInertiaException(Request $request, \Throwable $e)
    {
        switch (true) {
            case $e instanceof ValidationException:
                return back()->withErrors($e->errors());

            case $e instanceof AuthorizationException:
                return redirect('/')->withErrors([
                    'message' => 'Esta ação não é autorizada.'
                ]);

            case $e instanceof ModelNotFoundException:
                return redirect('/')->withErrors([
                    'message' => 'Recurso não encontrado.'
                ]);

            case $e instanceof QueryException:
                $errorsCode = [
                    '1400' => 'Não será possível continuar com a inclusão, existe campo que não pode ser vazio.',
                    '1407' => 'Não será possível continuar com a atualização, existe campo que não pode ser vazio.',
                    '2292' => 'Não será possível continuar com a exclusão, ela é referenciada por outros registros no banco de dados.',
                ];
                $errorCodeUnregistered = 'Não foi possível executar a instrução no banco de dados.';
                $message = $errorsCode[$e->getCode()] ?? $errorCodeUnregistered;
                return back()->withErrors(['message' => $message]);

            case $e instanceof JWTException:
                return redirect('/')->withErrors([
                    'message' => 'Sua sessão expirou. Por favor, faça login novamente.'
                ]);

            default:
                return redirect('/')->withErrors([
                    'message' => 'Ocorreu um erro inesperado.'
                ]);
        }
    }
} 