<?php

    namespace App\Traits;

    use App\Models\PostulacionAsignacion;
    use App\Models\PostulacionEstado;
    use App\Models\SeguimientoPostulacion;
    use App\Models\User;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;
    use Illuminate\Validation\ValidationException;

    trait HasFlujoPostulacion
    {
        public static function transicionesPermitidas(): array
        {
            return [
                'recibida' => ['en_revision', 'descartada'],
                'en_revision' => ['recibida', 'entrevista', 'descartada'],
                'entrevista' => ['en_revision', 'contratado', 'descartada'],
                'contratado' => [],
                'descartada' => [],
            ];
        }

        public function estadosDisponibles(): array
        {
            return self::transicionesPermitidas()[$this->estado?->codigo] ?? [];
        }

        public function validarMovimiento(PostulacionEstado $destino, ?array $transiciones = null): void
        {
            $permitidos = $this->estadosDisponibles();

            if ($transiciones !== null) {
                $permitidos = $transiciones[$this->estado?->codigo] ?? [];
            }

            if (!in_array($destino->codigo, $permitidos, true)) {
                throw ValidationException::withMessages([
                    'estado_id' => 'El movimiento al estado seleccionado no está permitido.',
                ]);
            }
        }

        public function estaFinalizada(): bool
        {
            return $this->estado?->bandeja?->codigo === 'finalizadas';
        }

        public function moverA(
            PostulacionEstado $destino,
            string $comentario,
            ?int $userId = null,
            ?float $porcentaje = null,
            ?float $puntajeFase = null
        ): SeguimientoPostulacion {
            $this->validarMovimiento($destino);

            return DB::transaction(function () use ($destino, $comentario, $userId, $porcentaje, $puntajeFase) {
                $estadoAnteriorId = $this->estado_id;
                $origen = $this->estado?->nombre;

                $this->update(['estado_id' => $destino->id]);
                $this->setRelation('estado', $destino);

                $this->asignacion()->update(['bandeja_id' => $destino->bandeja_id]);

                $this->addBitacora(
                    'Postulación',
                    'Cambio de estado',
                    "Estado cambió de «{$origen}» a «{$destino->nombre}». Comentario: {$comentario}",
                    'tabler-arrows-exchange',
                    'primary',
                    $userId
                );

                return $this->registrarSeguimiento(
                    $destino->id,
                    $estadoAnteriorId,
                    $comentario,
                    $userId,
                    $porcentaje,
                    $puntajeFase
                );
            });
        }

        public function asignarA(int $reclutadorId, ?int $userId = null): PostulacionAsignacion
        {
            if ($this->estaFinalizada()) {
                throw ValidationException::withMessages([
                    'postulacion' => 'No se puede asignar una postulación finalizada.',
                ]);
            }

            $reclutador = User::reclutadores()->find($reclutadorId);

            if ($reclutador === null) {
                throw ValidationException::withMessages([
                    'users_id' => 'El usuario seleccionado no es un reclutador activo.',
                ]);
            }

            return DB::transaction(function () use ($reclutador, $userId) {
                $asignacion = PostulacionAsignacion::updateOrCreate(
                    ['postulacion_id' => $this->id],
                    [
                        'users_id' => $reclutador->id,
                        'bandeja_id' => $this->estado->bandeja_id,
                        'nombre' => Str::limit($reclutador->nombre_completo, 45, ''),
                    ]
                );

                $this->addBitacora(
                    'Postulación',
                    'Postulación asignada',
                    "Postulación asignada a {$reclutador->nombre_completo}.",
                    'tabler-user-check',
                    'success',
                    $userId
                );

                $this->registrarSeguimiento(
                    $this->estado_id,
                    $this->estado_id,
                    "Postulación asignada a {$reclutador->nombre_completo}",
                    $userId
                );

                return $asignacion;
            });
        }

        public function comentar(string $comentario, ?int $userId = null)
        {
            return $this->addBitacora('Postulación', 'Comentario agregado', $comentario, 'tabler-message', 'info', $userId);
        }

        public function registrarSeguimiento(
            int $estadoId,
            ?int $estadoAnteriorId,
            string $comentario,
            ?int $userId,
            ?float $porcentaje = null,
            ?float $puntajeFase = null
        ): SeguimientoPostulacion {
            return $this->seguimientos()->create([
                'estado_id' => $estadoId,
                'estado_anterior_id' => $estadoAnteriorId,
                'comentario' => $comentario,
                'porcentaje' => $porcentaje ?? $this->seguimientos()->latest('id')->value('porcentaje') ?? 0,
                'puntaje_fase' => $puntajeFase,
                'user_id' => $userId,
                'fecha_cambio' => now(),
            ]);
        }
    }
