<?php

namespace App\Exceptions;

use App\Services\ExceptionHelper;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler extends Handler
{
    public function register()
    {

        $this->renderable(function (Throwable $e, Request $request) {
            if ($this->isApiRequest($request)) {
                // Manejo de AuthenticationException
                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('auth.unauthenticated'), // Traducción
                        'errors' => null,
                    ], 401);
                }
                // Manejo de HttpException
                if ($e instanceof HttpException) {
                    return $this->handleException($e);
                }
            }
        });

        parent::register();
    }

    protected function handleException($e)
    {
        if ($e instanceof HttpException && $e->getMessage() === 'Invalid ability provided.') {
            return response()->json([
                'status' => 'error',
                'message' => __('auth.invalid_ability'), // Traducción personalizada
                'errors' => null,
            ], $e->getStatusCode());
        }

        if ($e->getStatusCode()===404) {
            return ExceptionHelper::handleModelNotFoundException($e);
        }

        if ($e->getStatusCode()===429) {
            return ExceptionHelper::handleThrottleRequestsException($e);
        }

        if ($e->getStatusCode()===405) {
            return ExceptionHelper::handleMethodNotAllowedHttpException($e);
        }

        if ($e->getStatusCode()>=500) {
            return ExceptionHelper::handleQueryException($e);
        }

        return ExceptionHelper::handleHttpException($e);

    }

    protected function isApiRequest(Request $request): bool
    {
        return $request->is('api/*');
    }
}
