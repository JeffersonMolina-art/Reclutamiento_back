<?php

    namespace App\Console\Commands;

    use Dflydev\DotAccessData\Data;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Process;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;

    class GeneradorCrudCommandV3 extends Command
    {
        protected $signature = 'generar:crud {table : Nombre de la tabla} {modelo : Nombre del modelo} {connection? : Nombre de la conexión (opcional)}';
        protected $description = 'Generar API incluyendo Modelo, Controlador y Requests';

        private $conexion;
        private $schema;
        private $nombreTabla;
        private $nombreModelo;
        private $variable;
        private $variablePlural;
        private $namespace = 'App\\Models';
        private $controllerNamespace = 'App\\Http\\Controllers\\Api';
        private $requestNamespace = 'App\\Http\\Requests\\Api';
        private $seederNamespace = 'Database\\Seeders';

        private $castFormatoOriginal;

        private $columnasDeTabla;
        private $subdirectorio;

        public function handle()
        {

            $this->conexion = $this->argument('connection') ?? config('database.default'); // Nombre de la conexión
            $this->schema = Schema::connection($this->conexion);    // Esquema de la base de datos
            $this->nombreTabla = $this->argument('table');  // Nombre de la tabla
            $this->nombreModelo = Str::studly($this->argument('modelo')); // Nombre del modelo
            $subdirectorio = $this->ask('¿Deseas generar los archivos dentro de un subdirectorio? (Ej: Admin/Usuarios, ENTER para usar la ubicación por defecto)');
            $this->subdirectorio = trim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $subdirectorio));
            $this->variable = strtolower($this->nombreModelo); // Nombre de la variable en singular (ej: `user`)
            $this->variablePlural = Str::kebab(Str::pluralStudly($this->nombreModelo)); // Nombre de recurso en kebab-case (ej: `users`)

            if($this->subdirectorio) {
                $this->namespace .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $this->subdirectorio);
                $this->controllerNamespace .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $this->subdirectorio);
                $this->requestNamespace .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $this->subdirectorio);
                $this->seederNamespace = 'Database\\Seeders\\' . $this->subdirectorio;
            }

            // Verificar si la tabla existe
            if (!$this->schema->hasTable($this->nombreTabla)) {
                $this->error("La tabla '{$this->nombreTabla}' no existe.");
                return;
            }

//        $useSoftDeletes = $this->confirm('¿Requiere SoftDeletes?', false);

            /**
             * Generar contenido de los archivos
             */
            $this->columnasDeTabla = $this->schema->getColumnListing($this->nombreTabla);
            $fillable = $this->generateFillable();
            $validationRules = $this->generateValidationRules();
            $relationships = $this->generateRelationships();
            $casts = $this->generateCasts();


            $this->generateFiles($fillable, $validationRules, $relationships, $casts);

            // Añadir la ruta
            $this->addRoute();

            $this->info("🚀 ¡Generación de archivos completada con éxito!");

        }


        private function generateFiles($fillable, $validationRules, $relationships, $casts)
        {
            $tableNameM = ucfirst($this->nombreTabla);

            $pathSub = $this->subdirectorio ? $this->subdirectorio . DIRECTORY_SEPARATOR : '';

            $nombreTabla = str_contains($this->nombreTabla, '.') ? explode('.', $this->nombreTabla)[1] : $this->nombreTabla;

            $modelo = "{$this->nombreModelo}";
            $controlador = "{$this->nombreModelo}ApiController";
            $createRequest = "Create{$this->nombreModelo}ApiRequest";
            $updateRequest = "Update{$this->nombreModelo}ApiRequest";
            $seederName =  ucwords($this->nombreModelo) . "TableSeeder";
            $seederPermisosName =  ucwords($this->nombreModelo) . "PermisosTableSeeder";

            $modelPath = app_path("Models/{$pathSub}{$modelo}.php");
            $controllerPath = app_path("Http/Controllers/Api/{$pathSub}{$controlador}.php");
            $createRequestPath = app_path("Http/Requests/Api/{$pathSub}{$createRequest}.php");
            $updateRequestPath = app_path("Http/Requests/Api/{$pathSub}{$updateRequest}.php");
            $seederPath = database_path("seeders/{$pathSub}{$seederName}.php");
            $seederPermisosPath = database_path("seeders/{$pathSub}{$seederPermisosName}.php");

            // Stubs
            $modelStub = File::get(base_path('stubs/api/model.stub'));
            $controllerStub = File::get(base_path('stubs/api/controller.stub'));
            $createRequestStub = File::get(base_path('stubs/api/request-create.stub'));
            $updateRequestStub = File::get(base_path('stubs/api/request-update.stub'));
            $seederStub = File::get(base_path('stubs/api/seeder.stub'));
            $seederPermisosStub = File::get(base_path('stubs/api/seeder-permisos.stub'));

            $tieneSoftDeletes = Schema::hasColumn($this->nombreTabla, 'deleted_at'); // Verificar si la tabla tiene SoftDeletes

            $useSoftDeletesCode = $tieneSoftDeletes ? 'use Illuminate\\Database\\Eloquent\\SoftDeletes;' : '';
            $softDeletesTrait = $tieneSoftDeletes ? 'use SoftDeletes;' : '';

            $replacements = [
                '{{ modelNamespace }}' => $this->namespace,
                '{{ requestNamespace }}' => $this->requestNamespace,
                '{{ controllerNamespace }}' => $this->controllerNamespace,
                '{{ seederNamespace }}' => $this->seederNamespace,
                '{{ controlador }}' => $controlador,
                '{{ model }}' => $this->nombreModelo,
                '{{ variable }}' => $this->variable,
                '{{ variable_plural }}' => $nombreTabla,
                '{{ nombre_permiso }}' => $this->camelCaseAFraseMinusculaConPlural($this->nombreModelo),
                '{{ tableNameM }}' => $tableNameM,
                '{{ seederPermisosName }}' => $seederPermisosName,
                '{{ seederName }}' => $seederName,
                '{{ createRequest }}' => $createRequest,
                '{{ updateRequest }}' => $updateRequest,
                '{{ fillable }}' => $fillable,
                '{{ validationRules }}' => $validationRules,
                '{{ relationships }}' => $relationships,
                '{{ tableName }}' => $this->nombreTabla,
                '{{ useSoftDeletes }}' => $useSoftDeletesCode,
                '{{ softDeletesTrait }}' => $softDeletesTrait,
                '{{ casts }}' => $casts,
                '{{ otro }}' => implode(",\n                    ", array_map(fn($campo) => "'$campo'", array_diff($this->columnasDeTabla, ['id', 'created_at', 'updated_at', 'deleted_at']))),
            ];

            foreach ([$modelPath, $controllerPath, $createRequestPath, $updateRequestPath, $seederPath, $seederPermisosPath] as $path) {
                if (!File::exists(dirname($path))) {
                    File::makeDirectory(dirname($path), 0755, true);
                }
            }

            // Generar archivos
            File::put($modelPath, str_replace(array_keys($replacements), $replacements, $modelStub));
            File::put($controllerPath, str_replace(array_keys($replacements), $replacements, $controllerStub));
            File::put($createRequestPath, str_replace(array_keys($replacements), $replacements, $createRequestStub));
            File::put($updateRequestPath, str_replace(array_keys($replacements), $replacements, $updateRequestStub));
            File::put($seederPath, str_replace(array_keys($replacements), $replacements, $seederStub));
            File::put($seederPermisosPath, str_replace(array_keys($replacements), $replacements, $seederPermisosStub));

            // Ejecutar el comando ide-helper:models para agregar anotaciones
            $this->callSilent('ide-helper:models', [
                'model' => [$this->namespace.'\\'.$modelo],
                '--reset' => true,
                '--write' => true,
                '--no-interaction' => true, // Evita cualquier interacción del usuario
            ]);

            $this->newLine();

            $this->info("📁 Estructura de Archivos:");

            $this->line("  ├── <fg=blue>📂 app</>");
            $this->line("  │   ├── <fg=blue>📂 Models</>");
            $this->line("  │   │   └── <fg=green>📄 {$modelo}.php</>");
            $this->line("  │   ├── <fg=blue>📂 Http</>");
            $this->line("  │   │   ├── <fg=blue>📂 Controllers</>");
            $this->line("  │   │   │   └── <fg=green>📄 {$controlador}.php</>");
            $this->line("  │   │   └── <fg=blue>📂 Requests</>");
            $this->line("  │   │       ├── <fg=green>📄 {$createRequest}.php</>");
            $this->line("  │   │       └── <fg=green>📄 {$updateRequest}.php</>");
            $this->line("  ├── <fg=blue>📂 database</>");
            $this->line("  │   └── <fg=blue>📂 seeders</>");
            $this->line("  │       └── <fg=green>📄 {$this->nombreTabla}TableSeeder.php</>");
            $this->line("  └── <fg=blue>📂 routes</>");
            $this->line("      └── <fg=green>📄 api.php</>");

            $this->newLine();

            $datosACopiar = json_encode([
                'Modelo' => $modelo,
                'Ruta' => "api/{$nombreTabla}",
                'Columnas' => $this->camposTipados(),
                'Directorio' => $this->subdirectorio,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            $this->line("\n📋 Copia el JSON generado aquí 👇");
            $this->line(str_repeat('═', 50));
            $this->line($datosACopiar);
            $this->line(str_repeat('═', 50) . "\n");

            if (PHP_OS_FAMILY === 'Darwin') {
                Process::run("echo " . escapeshellarg($datosACopiar) . " | pbcopy");
                $this->info("✅ JSON copiado al portapapeles (MacOS)");
            } elseif (PHP_OS_FAMILY === 'Windows') {
                $process = new \Symfony\Component\Process\Process(['clip']);
                $process->setInput($datosACopiar);
                $process->run();

                if ($process->isSuccessful()) {
                    $this->info("✅ JSON copiado al portapapeles (Windows)");
                } else {
                    $this->error("No se pudo copiar el JSON.");
                }
            } else {
                $this->warn("⚠️ Copia manual: selecciona el texto y usa Ctrl+C");
            }
        }


        /**
         * Añadir ruta al archivo de rutas
         *
         * @param  string  $name
         * @return void
         */
        private function addRoute()
        {
            $routePath = base_path('routes/api.php');
            $resourceName = $this->variablePlural; // Nombre de recurso en kebab-case (ej: `users`)
            $controllerName = "{$this->nombreModelo}ApiController";
            $nombreTabla = str_contains($this->nombreTabla, '.') ? explode('.', $this->nombreTabla)[1] : $this->nombreTabla;

            $controladorCompleto = $this->controllerNamespace . '\\' . $controllerName;

            $route = "Route::apiResource('{$nombreTabla}', {$controladorCompleto}::class)
        ->parameters(['{$nombreTabla}' => '{$this->variable}']);";


            if (!File::exists($routePath)) {
                $this->error("El archivo de rutas 'routes/api.php' no existe.");
                return;
            }

            $content = File::get($routePath);

            // Verificar si la ruta ya existe
            if (strpos($content, $route) !== false) {
                $this->warn("La ruta para '{$resourceName}' ya existe en el archivo de rutas.");
                return;
            }

            // Añadir la nueva ruta al final del archivo
            File::append($routePath, PHP_EOL.$route.PHP_EOL);

            $this->newLine();

            $this->info("🌐 Ruta añadida al archivo 'routes/api.php exitosamente!'");

            $this->newLine();

        }


        //TODO: Cambiar aqui la generacion filiables
        private function generateFillable()
        {
            // Excluir columnas que no son relevantes para $fillable
            $excluded = ['id', 'created_at', 'updated_at', 'deleted_at'];
            $fillable = array_diff($this->columnasDeTabla, $excluded);

            return '['.PHP_EOL.'    \''.implode("',\n    '", $fillable).'\''.PHP_EOL.']';
        }

        private function generateValidationRules(): string
        {
            // Obtener información de las columnas
            $columns = $this->schema->getColumns($this->nombreTabla);
            $indexes = $this->schema->getIndexes($this->nombreTabla);


            $uniqueColumns = array_filter($indexes, function ($index) {
                return $index['unique']; // Filtrar los índices únicos
            });

            $rules = [];

            foreach ($columns as $column) {
                $columnName = $column['name'];


                // Excluir columnas que no son relevantes para las reglas de validación
                if (in_array($columnName, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                    continue;
                }

                $typeName = $column['type_name'];
                $nullable = $column['nullable'];
                $typeDetails = $column['type']; // Contiene información como nvarchar(255)

                $columnRules = [];

                // Requerido o nullable
                $columnRules[] = $nullable ? 'nullable' : 'required';

                // Validar tipo de dato
                switch ($typeName) {
                    case 'nvarchar':
                    case 'varchar':
                    case 'char':
                    case 'text':
                        $columnRules[] = 'string';

                        // Extraer longitud máxima si está disponible (por ejemplo, nvarchar(255))
                        if (preg_match('/\((\d+)\)/', $typeDetails, $matches)) {
                            $maxLength = $matches[1];
                            $columnRules[] = "max:$maxLength";
                        }
                        break;

                    case 'int':
                    case 'bigint':
                    case 'smallint':
                    case 'tinyint':
                        $columnRules[] = 'integer';
                        break;

                    case 'decimal':
                    case 'float':
                    case 'double':
                        $columnRules[] = 'numeric';
                        break;

                    case 'date':
                    case 'datetime':
                    case 'timestamp':
                        $columnRules[] = 'date';
                        break;

                    case 'bit':
                    case 'boolean':
                        $columnRules[] = 'boolean';
                        break;

                    default:
                        $columnRules[] = 'string'; // Tipo genérico por defecto
                }

                // Validar unicidad
                foreach ($uniqueColumns as $index) {
                    if (in_array($columnName, $index['columns'])) {
                        $columnRules[] = "unique:{$this->nombreTabla},{$columnName}";
                        break;
                    }
                }

                // Agregar las reglas al array principal
                $rules[$columnName] = implode('|', $columnRules);
            }

            // Formatear las reglas en un array PHP
            $formattedRules = '['.PHP_EOL;
            foreach ($rules as $field => $rule) {
                $formattedRules .= "    '$field' => '$rule',".PHP_EOL;
            }
            $formattedRules .= ']';

            return $formattedRules;
        }

        private function generateCasts(): string
        {
            $columns = $this->schema->getColumns($this->nombreTabla);
            $casts = [];

            foreach ($columns as $column) {
                $columnName = $column['name'];
                $typeName = $column['type_name'];

                switch ($typeName) {
                    case 'int':
                    case 'bigint':
                    case 'smallint':
                    case 'tinyint':
                        $casts[$columnName] = 'integer';
                        break;

                    case 'decimal':
                    case 'float':
                    case 'double':
                        $casts[$columnName] = 'float';
                        break;

                    case 'bit':
                    case 'boolean':
                        $casts[$columnName] = 'boolean';
                        break;

                    case 'date':
                        $casts[$columnName] = 'date';
                        break;
                    case 'datetime':
                        $casts[$columnName] = 'datetime';
                        break;
                    case 'timestamp':
                        $casts[$columnName] = 'timestamp';
                        break;

                    default:
                        $casts[$columnName] = 'string';
                }
            }

            $this->castFormatoOriginal = $casts;

            $formattedCasts = '['.PHP_EOL;
            foreach ($casts as $field => $cast) {
                $formattedCasts .= "        '$field' => '$cast',".PHP_EOL;
            }
            $formattedCasts .= '    ]';

            return $formattedCasts;

        }

        private function generateRelationships(): string
        {
            $foreignKeys = $this->schema->getForeignKeys($this->nombreTabla);
            $relationships = [];

            foreach ($foreignKeys as $foreignKey) {
                $columnName = $foreignKey['columns'][0]; // Columna de la tabla actual
                $referencedTable = $foreignKey['foreign_table']; // Tabla referenciada
                $referencedColumn = $foreignKey['foreign_columns'][0]; // Columna referenciada


                // Determinar el tipo de relación
                $relationType = $this->determineRelationshipType($foreignKey, $this->nombreTabla);
                $relatedModel = Str::studly(Str::singular($referencedTable)); // Modelo relacionado
                $functionName = ($relationType === 'hasMany' || $relationType === 'belongsToMany')
                    ? Str::camel(Str::plural($referencedTable))
                    : Str::camel(Str::singular($referencedTable));

                // Generar el método de relación
                $relationships[] = <<<EOT
        public function {$functionName}()
            {
            return \$this->{$relationType}({$relatedModel}::class,'{$columnName}','{$referencedColumn}');
            }
        EOT;
            }

            return implode("\n\n    ", $relationships);
        }

        private function determineRelationshipType(array $foreignKey, string $nombreTabla): string
        {
            $columnName = $foreignKey['columns'][0];
            $referencedTable = $foreignKey['foreign_table'];
            $referencedColumn = $foreignKey['foreign_columns'][0];

            // belongsTo
            if ($nombreTabla !== $referencedTable) {
                return 'belongsTo';
            }

            // hasMany
            if ($nombreTabla === $referencedTable && $referencedColumn === 'id') {
                return 'hasMany';
            }

            // hasOne (si la clave foránea es única)
            if ($this->isUniqueForeignKey($nombreTabla, $columnName)) {
                return 'hasOne';
            }

            // belongsToMany (tabla pivote)
            if ($this->isPivotTable($nombreTabla)) {
                return 'belongsToMany';
            }

            // Polimórficas
            if ($this->isPolymorphic($foreignKey)) {
                return 'morphTo';
            }

            // Por defecto
            return '';
        }

        private function isPivotTable(string $nombreTabla): bool
        {
            $foreignKeys = $this->schema->getForeignKeys($nombreTabla);

            // Una tabla pivote generalmente tiene exactamente 2 claves foráneas
            return count($foreignKeys) === 2 && $this->schema->getColumnCount($nombreTabla) <= 3;
        }

        private function isUniqueForeignKey(string $nombreTabla, string $nombreColumna): bool
        {
            $indexes = $this->schema->getIndexes($nombreTabla);

            foreach ($indexes as $index) {
                if (in_array($nombreColumna, $index['columns']) && $index['unique']) {
                    return true;
                }
            }

            return false;
        }

        private function isPolymorphic(array $columns): bool
        {
            return in_array('morph_id', $columns) && in_array('morph_type', $columns);
        }

        private function camposTipados(): array
        {

            $camposSinTimestamp = array_diff($this->columnasDeTabla, ['id', 'created_at', 'updated_at', 'deleted_at']);

            return array_filter($this->castFormatoOriginal, function ($key) use ($camposSinTimestamp) {
                return in_array($key, $camposSinTimestamp);
            }, ARRAY_FILTER_USE_KEY);

        }

        function camelCaseAFraseMinusculaConPlural($texto) {
            // Insertar espacios entre palabras camel case
            $frase = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $texto);
            $frase = preg_replace('/(?<=[A-Z])(?=[A-Z][a-z])/', ' ', $frase);

            // Convertir a minúsculas
            $palabras = explode(' ', strtolower($frase));

            // Pluralizar la última palabra
            $ultima = array_pop($palabras);
            $plural = $ultima;

            if (str_ends_with($ultima, 'z')) {
                $plural = substr($ultima, 0, -1) . 'ces';
            } elseif (str_ends_with($ultima, 'ión')) {
                $plural = preg_replace('/ión$/', 'iones', $ultima);
            } elseif (str_ends_with($ultima, 's')) {
                // Ya es plural
            } elseif (preg_match('/[aeo]$/', $ultima)) {
                $plural = $ultima . 's';
            } else {
                $plural = $ultima . 'es';
            }

            $palabras[] = $plural;

            return implode(' ', $palabras);
        }


    }
