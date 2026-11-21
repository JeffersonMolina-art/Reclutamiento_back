<?php

namespace App\Services;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;

class ExceptionHelper
{
    public static function handleHttpException(HttpException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'errors' => null,
        ], $e->getStatusCode());
    }


    public static function handleModelNotFoundException(\Exception $e): JsonResponse
    {
        if ($e->getPrevious()) {
            $model = class_basename($e->getPrevious()->getModel());
            $id = $e->getPrevious()->getIds()[0];

            return response()->json([
                'status' => 'error',
                'message' => "No se encontro el modelo $model con el id $id",
                'errors' => null,
            ], 404);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No se encontro el modelo',
            'errors' => null,
        ], 404);
    }

    public static function handleThrottleRequestsException(ThrottleRequestsException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Has realizado demasiadas solicitudes. Intenta nuevamente más tarde.',
            'errors' => null,
        ], 429);
    }

    public static function handleValidationException(ValidationException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Error de validación',
            'errors' => $e->errors(),
        ], 422);
    }

    public static function handleQueryException(QueryException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Error interno en la base de datos',
            'errors' => null,
        ], 500);
    }

    public static function handleMethodNotAllowedHttpException(MethodNotAllowedHttpException $e): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Método no permitido',
            'errors' => null,
        ], 405);
    }

}
