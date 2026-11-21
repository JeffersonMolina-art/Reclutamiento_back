<?php

    namespace App\Http\Controllers;

    use Illuminate\Support\Facades\DB;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Response;
    use App\Utils\ResponseUtil;
    use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
    use Throwable;


    /**
     * @SWG\Swagger(
     *   basePath="/api/v1",
     *   @SWG\Info(
     *     title="Laravel Generator APIs",
     *     version="1.0.0",
     *   )
     * )
     * This class should be parent class for other API controllers
     * Class AppBaseController
     */
    class AppBaseController extends Controller
    {
        /**
         * @param string $message
         * @param mixed  $data
         *
         * @return array
         */
        public static function makeResponse($message, $data)
        {
            return [
                'success' => true,
                'data'    => $data,
                'message' => $message,
            ];
        }
        public function sendResponse($result, $message)
        {
            return Response::json(ResponseUtil::makeResponse($message, $result));
        }
        public function sendError($error, $code = 404)
        {
            return Response::json(ResponseUtil::makeError($error), $code);
        }
        public function sendSuccess($message)
        {
            return Response::json([
                'success' => true,
                'message' => $message
            ], 200);
        }

        /**
         * Ejecuta una transacción de base de datos y maneja las excepciones.
         */
        protected function executeTransaction(callable $callback): JsonResponse
        {
            try {
                return DB::transaction($callback);
            } catch (Throwable $e) {
                return $this->sendError(manejarException($e),  $this->getHttpStatus($e));
            }
        }

        /**
         * Ejecuta una operación y maneja las excepciones.
         */
        protected function executeOperation(callable $callback): JsonResponse
        {
            try {
                return $callback();
            } catch (Throwable $e) {
                return $this->sendError(manejarException($e),  $this->getHttpStatus($e));
            }
        }
        protected function getHttpStatus(Throwable $e): int
        {
            $status = (int) $e->getCode();

            return ($status >= 100 && $status <= 599)
                ? $status
                : 500;
        }
    }
