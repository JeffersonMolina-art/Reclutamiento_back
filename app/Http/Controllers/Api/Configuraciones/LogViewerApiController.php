<?php

    namespace App\Http\Controllers\Api\Configuraciones;

    use App\Http\Controllers\AppBaseController;
    use App\Models\User;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Routing\Controllers\HasMiddleware;
    use Illuminate\Routing\Controllers\Middleware;
    use Illuminate\Support\Facades\Log;
    use Throwable;

    class LogViewerApiController extends AppBaseController implements HasMiddleware
    {

        public static function middleware(): array
        {
            return [
                new Middleware('abilities:listar logs', only: ['menu', 'show', 'download']),
                new Middleware('abilities:borrar logs', only: ['delete']),
            ];
        }

        /**
         * Menú de navegación con logs agrupados
         */
        public function menu(): JsonResponse
        {
            $limite_pagina_front = request('page.size', 15);

            // 🔥 REEMPLAZO: lectura directa de logs backend
            $backendMenu = collect();

            $files = collect(glob(storage_path('logs/laravel-*.log')));

            foreach ($files as $filePath) {

                preg_match('/(\d{4}-\d{2}-\d{2})/', $filePath, $match);
                $fecha = $match[1] ?? null;

                if (!$fecha) continue;

                $contenido = file_get_contents($filePath);

                $levels = [
                    'error' => substr_count($contenido, '.ERROR:'),
                    'warning' => substr_count($contenido, '.WARNING:'),
                    'notice' => substr_count($contenido, '.NOTICE:'),
                    'info' => substr_count($contenido, '.INFO:'),
                    'debug' => substr_count($contenido, '.DEBUG:'),
                ];

                $backendMenu->put($fecha, [
                    'all' => ['count' => array_sum($levels)],
                    'error' => ['count' => $levels['error']],
                    'warning' => ['count' => $levels['warning']],
                    'notice' => ['count' => $levels['notice']],
                    'info' => ['count' => $levels['info']],
                    'debug' => ['count' => $levels['debug']],
                ]);
            }

            $frontendMenu = $this->obtenerMenuFrontendAgrupado();

            $fechasBackend = $backendMenu->keys();
            $fechasFrontend = $frontendMenu->pluck('fecha');

            $fechas = $fechasBackend->merge($fechasFrontend)->unique()->sortDesc()->values();

            $menuData = $fechas->map(function ($fecha) use ($backendMenu, $frontendMenu) {

                $backend = $backendMenu[$fecha] ?? [];
                $frontend = $frontendMenu->firstWhere('fecha', $fecha);

                return [
                    'fecha' => $fecha,
                    'all' => [
                        'count' => ($backend['all']['count'] ?? 0) + ($frontend['all']['count'] ?? 0),
                    ],
                    'error' => [
                        'count' => ($backend['error']['count'] ?? 0) + ($frontend['error']['count'] ?? 0),
                    ],
                    'warning' => [
                        'count' => ($backend['warning']['count'] ?? 0) + ($frontend['warning']['count'] ?? 0),
                    ],
                    'notice' => [
                        'count' => ($backend['notice']['count'] ?? 0) + ($frontend['notice']['count'] ?? 0),
                    ],
                    'info' => [
                        'count' => ($backend['info']['count'] ?? 0) + ($frontend['info']['count'] ?? 0),
                    ],
                    'debug' => [
                        'count' => ($backend['debug']['count'] ?? 0) + ($frontend['debug']['count'] ?? 0),
                    ],
                ];
            });

            $page = request('page.number', 1);

            $paginatedData = new LengthAwarePaginator(
                $menuData->forPage($page, $limite_pagina_front)->values(),
                $menuData->count(),
                $limite_pagina_front,
                $page,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );

            return $this->sendResponse($paginatedData, 'Menú de navegación con logs agrupados');
        }

        private function obtenerMenuFrontendAgrupado()
        {
            $path = storage_path('logs');

            if (!is_dir($path)) return collect();

            $files = collect(scandir($path))
                ->filter(fn($f) => str_contains($f, 'frontend-originador'))
                ->values();

            return $files->map(function ($file) use ($path) {

                preg_match('/(\d{4}-\d{2}-\d{2})/', $file, $match);
                $fecha = $match[1] ?? null;

                $contenido = file_get_contents($path.'/'.$file);

                $levels = [
                    'error' => substr_count($contenido, '.ERROR:'),
                    'warning' => substr_count($contenido, '.WARNING:'),
                    'notice' => substr_count($contenido, '.NOTICE:'),
                    'info' => substr_count($contenido, '.INFO:'),
                    'debug' => substr_count($contenido, '.DEBUG:'),
                ];

                return [
                    'fecha' => $fecha,
                    'all' => ['count' => array_sum($levels)],
                    'error' => ['count' => $levels['error']],
                    'warning' => ['count' => $levels['warning']],
                    'notice' => ['count' => $levels['notice']],
                    'info' => ['count' => $levels['info']],
                    'debug' => ['count' => $levels['debug']],
                ];
            });
        }

        /**
         * Obtener logs de una fecha específica
         */
        public function show($date): JsonResponse
        {
            try {

                $source = request('source', 'backend');

                $logPath = storage_path("logs/laravel-{$date}.log");
                $frontendPath = storage_path("logs/frontend-originador-{$date}.log");

                $backendLogs = $this->obtenerLogsBackend($logPath);
                $frontendLogs = $this->obtenerLogsFrontend($frontendPath);

                if ($source === 'frontend') {
                    $allLogs = $frontendLogs;
                } elseif ($source === 'backend') {
                    $allLogs = $backendLogs;
                } else {
                    $allLogs = $backendLogs->merge($frontendLogs);
                }

                $allLogs = $allLogs->reverse()->values();

                $limite_pagina_front = request('page.size', 10);
                $page = request('page.number', 1);
                $level = request('level', 'all');

                if ($level !== 'all') {
                    $allLogs = $allLogs->filter(function ($entry) use ($level) {
                        return $entry['level'] === $level;
                    })->values();
                }

                $paginatedLogs = new LengthAwarePaginator(
                    $allLogs->forPage($page, $limite_pagina_front)->values(),
                    $allLogs->count(),
                    $limite_pagina_front,
                    $page,
                    [
                        'path' => request()->url(),
                        'query' => request()->query(),
                    ]
                );

                $statsLogs = $allLogs
                    ->groupBy('level')
                    ->map(fn($items) => $items->count())
                    ->toArray();

                $stats = array_merge([
                    'all' => $allLogs->count(),
                    'error' => 0,
                    'warning' => 0,
                    'notice' => 0,
                    'info' => 0,
                    'debug' => 0,
                ], $statsLogs);

                $response = [
                    'date' => $date,
                    'path' => $source === 'frontend' ? $frontendPath : $logPath,
                    'environment' => config('app.env'),
                    'logs' => $paginatedLogs->items(),
                    'stats' => $stats,
                    'pagination' => [
                        'current_page' => $paginatedLogs->currentPage(),
                        'per_page' => $paginatedLogs->perPage(),
                        'total' => $paginatedLogs->total(),
                        'last_page' => $paginatedLogs->lastPage(),
                        'from' => $paginatedLogs->firstItem(),
                        'to' => $paginatedLogs->lastItem(),
                    ]
                ];

                return $this->sendResponse($response, 'Logs recuperados con éxito.');
            } catch (Throwable $e) {
                return $this->sendError(manejarException($e));
            }
        }
        private function obtenerLogsBackend($logPath)
        {
            $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            return collect($lines)->map(function ($line) {
                if (preg_match('/\[([^\]]+)\]\s+\w+\.(\w+):\s+(.+)/', $line, $matches)) {
                    $datetime = $matches[1];
                    $level = strtolower($matches[2]);
                    $resto = trim($matches[3]);

                    if (preg_match('/^(.+?)\s*(\{.+\})\s*$/s', $resto, $parts)) {
                        $header = trim($parts[1]);
                        $jsonStr = str_replace('\"', '"', $parts[2]);
                        $context = json_decode($jsonStr, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $context = json_decode($parts[2], true);
                        }

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $header = $resto;
                            $context = null;
                        }
                    } else {
                        $header = $resto;
                        $context = null;
                    }

                    return [
                        'datetime' => $datetime,
                        'level' => $level,
                        'header' => $header,
                        'stack' => '',
                        'context' => $context,
                    ];
                }
                return null;
            })->filter();
        }

        private function obtenerLogsFrontend($frontendPath)
        {
            if (!file_exists($frontendPath)) {
                return collect();
            }

            $content = file_get_contents($frontendPath);

            $entries = preg_split('/\n(?=\[[0-9]{4}-[0-9]{2}-[0-9]{2})/', $content);

            return collect($entries)->map(function ($entry) {

                if (preg_match('/\[([^\]]+)\]\s+\w+\.(\w+):\s+(.+)/s', $entry, $matches)) {
                    $datetime = $matches[1];
                    $level = strtolower($matches[2]);
                    $resto = trim($matches[3]);

                    $pos = strpos($resto, '{');

                    if ($pos !== false) {
                        $header = trim(substr($resto, 0, $pos));
                        $jsonStr = substr($resto, $pos);

                        $jsonStr = str_replace('\"', '"', $jsonStr);

                        $context = json_decode($jsonStr, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $context = json_decode($jsonStr, true, 512, JSON_PARTIAL_OUTPUT_ON_ERROR);
                        }

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $context = null;
                        }
                    } else {
                        $header = $resto;
                        $context = null;
                    }

                    return [
                        'datetime' => $datetime,
                        'level' => $level,
                        'header' => $header,
                        'stack' => '',
                        'context' => $context,
                    ];
                }

                return null;

            })->filter();
        }

        /**
         * Descargar archivo de log
         */
        public function download($date)
        {
            try {
                $source = request('source', 'backend');

                if ($source === 'frontend') {
                    $path = storage_path("logs/frontend-originador-{$date}.log");

                    if (!file_exists($path)) {
                        return $this->sendError('Log frontend no encontrado');
                    }

                    return response()->download($path, basename($path));
                }

                $path = storage_path("logs/laravel-{$date}.log");

                return response()->download($path, basename($path));

            } catch (Throwable $e) {
                return $this->sendError(manejarException($e));
            }
        }

        /**
         * Eliminar log(s)
         */
        public function delete(Request $request): JsonResponse
        {
            $date = $request->input('fecha');
            try {

                $backendPath = storage_path("logs/laravel-{$date}.log");
                $frontendPath = storage_path("logs/frontend-originador-{$date}.log");

                $result = false;

                if (file_exists($backendPath)) {
                    unlink($backendPath);
                    $result = true;
                }

                if (file_exists($frontendPath)) {
                    unlink($frontendPath);
                    $result = true;
                }

                return $this->sendResponse($result, 'Log eliminado exitosamente');

            } catch (Throwable $e) {
                return $this->sendError(manejarException($e));
            }
        }

        public function generar(Request $request)
        {
            $request->validate([
                'tipo' => 'required|in:error,warning,notice,info,debug',
            ]);

            $user = auth()->user() ?? auth('api')->user() ?? new User();

            $contexto = [
                'archivo' => __FILE__.' Linea: '.__LINE__,
                'request' => $request->all(),
                'usuario' => array_merge(
                    $user->toArray(),
                    [
                        'foto_perfil' => $user->foto_perfil ?? null,
                        'roles' => $user->roles?->pluck('name')->toArray() ?? [],
                        'puesto' => $user->puesto?->nombre,
                        'unidad' => $user->unidad?->nombre,
                    ]
                ),
            ];

            match ($request->tipo) {
                'error' => Log::error('Log de prueba ERROR', $contexto),
                'warning' => Log::warning('Log de prueba WARNING', $contexto),
                'notice' => Log::notice('Log de prueba NOTICE', $contexto),
                'info' => Log::info('Log de prueba INFO', $contexto),
                'debug' => Log::debug('Log de prueba DEBUG', $contexto),
            };

            return $this->sendResponse(
                null,
                "Log de tipo {$request->tipo} generado correctamente."
            );
        }

        public function registrarErrorFront(Request $request)
        {
            return $this->executeOperation(function () use ($request) {
                $user = usuarioAutenticado();
                $stack = $request->input('stack');
                $tipo = $request->input('tipo', 'error');

                if ($stack) {
                    $stack = explode("\n", $stack);
                }

                Log::channel('frontend_originador')->log(
                    $tipo,
                    $request->input('mensaje'),
                    [
                        'archivo' => $request->input('url'),
                        'usuario' => array_merge(
                            $user->toArray(),
                            [
                                'foto_perfil' => $user->foto_perfil ?? null,
                                'roles' => $user->roles?->pluck('name')->toArray() ?? [],
                                'puesto' => $user->puesto?->nombre,
                                'unidad' => $user->unidad?->nombre,
                            ]
                        ),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'request' => [
                            'url' => $request->input('url'),
                            'app' => $request->input('app'),
                            'mensaje' => $request->input('mensaje'),
                            'stack' => $stack,
                        ],
                    ]
                );

                return $this->sendResponse([], 'Log de frontend registrado exitosamente.');
            });
        }
    }
