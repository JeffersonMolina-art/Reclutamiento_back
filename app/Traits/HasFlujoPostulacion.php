<?php

    namespace App\Traits;

    use App\Models\Bandeja;
    use App\Models\PostulacionAsignacion;
    use App\Models\PostulacionEstado;
    use App\Models\Role;
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
                PostulacionEstado::RECIBIDA => [PostulacionEstado::EN_REVISION, PostulacionEstado::DESCARTADA],
                PostulacionEstado::EN_REVISION => [PostulacionEstado::RECIBIDA, PostulacionEstado::ENTREVISTA, PostulacionEstado::DESCARTADA],
                PostulacionEstado::ENTREVISTA => [PostulacionEstado::EN_REVISION, PostulacionEstado::OFERTA, PostulacionEstado::DESCARTADA],
                PostulacionEstado::OFERTA => [PostulacionEstado::CONTRATADO, PostulacionEstado::DESCARTADA],
                PostulacionEstado::CONTRATADO => [],
                PostulacionEstado::DESCARTADA => [],
            ];
        }
        public static function rolesResponsables(): array
        {
            return [
                PostulacionEstado::RECIBIDA => [Role::RECLUTADOR],
                PostulacionEstado::EN_REVISION => [Role::ANALISTA],
                PostulacionEstado::ENTREVISTA => [Role::RECLUTADOR],
                PostulacionEstado::OFERTA => [Role::RECLUTADOR],
            ];
        }

        public static function fases(): array
        {
            return [
                PostulacionEstado::RECIBIDA,
                PostulacionEstado::EN_REVISION,
                PostulacionEstado::ENTREVISTA,
                PostulacionEstado::OFERTA,
                PostulacionEstado::CONTRATADO,
            ];
        }

        public static function porcentajeDeFase(int $estadoId): ?float
        {
            $posicion = array_search($estadoId, self::fases(), true);

            return $posicion === false ? null : round($posicion / (count(self::fases()) - 1) * 100, 2);
        }

        private function esResponsableDe(?User $usuario, ?int $estadoId): bool
        {
            if ($usuario === null) {
                return false;
            }

            if ($usuario->hasAnyRole(Role::ROLES_ADMINISTRATIVOS)) {
                return true;
            }

            $roles = self::rolesResponsables()[$estadoId] ?? [];

            if ($roles !== [] && $usuario->hasAnyRole($roles)) {
                return true;
            }

            return $estadoId === PostulacionEstado::ENTREVISTA && $this->vacante?->user_encargado === $usuario->id;
        }

        public function puedeMoverPor(?User $usuario): bool
        {
            return $this->esResponsableDe($usuario, $this->estado_id);
        }

        public function estadosDisponibles(): array
        {
            return self::transicionesPermitidas()[$this->estado_id] ?? [];
        }

        public function validarMovimiento(PostulacionEstado $destino, ?array $transiciones = null): void
        {
            $permitidos = $this->estadosDisponibles();

            if ($transiciones !== null) {
                $permitidos = $transiciones[$this->estado_id] ?? [];
            }

            if (!in_array($destino->id, $permitidos, true)) {
                throw ValidationException::withMessages([
                    'estado_id' => 'El movimiento al estado seleccionado no está permitido.',
                ]);
            }
        }

        public function estaFinalizada(): bool
        {
            return $this->estado?->bandeja_id === Bandeja::FINALIZADAS;
        }

        public function moverA(PostulacionEstado $destino, string $comentario, ?int $userId = null, ?float $puntajeFase = null): SeguimientoPostulacion {
            $actor = $userId ? User::find($userId) : null;

            if ($actor !== null && !$this->puedeMoverPor($actor)) {
                throw ValidationException::withMessages([
                    'estado_id' => 'No tienes permiso para mover la postulación desde su estado actual.',
                ]);
            }

            $this->validarMovimiento($destino);

            return DB::transaction(function () use ($destino, $comentario, $userId, $puntajeFase) {
                $estadoAnteriorId = $this->estado_id;
                $origen = $this->estado?->nombre;

                $this->update(['estado_id' => $destino->id]);
                $this->setRelation('estado', $destino);

                $this->asignarEnBandeja($destino->bandeja_id, $this->resolverResponsable($destino, $userId));

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
                    self::porcentajeDeFase($destino->id),
                    $puntajeFase
                );
            });
        }

        private function resolverResponsable(PostulacionEstado $destino, ?int $userId): ?int
        {
            if ($destino->bandeja_id === Bandeja::FINALIZADAS) {
                return $this->asignacion?->users_id ?? $userId;
            }

            if ($destino->id === PostulacionEstado::EN_REVISION) {
                return $this->analistaConMenosCarga();
            }

            return $this->vacante?->user_encargado
                ?? $this->asignacion?->users_id
                ?? $userId;
        }

        private function analistaConMenosCarga(): int
        {
            $ids = User::analista()->pluck('id');

            if ($ids->isEmpty()) {
                throw ValidationException::withMessages([
                    'estado_id' => 'No hay analistas activos para asignar la revisión.',
                ]);
            }

            $cargas = PostulacionAsignacion::whereIn('users_id', $ids)
                ->whereIn('id', PostulacionAsignacion::query()
                    ->selectRaw('MAX(id)')
                    ->groupBy('postulacion_id'))
                ->whereHas('postulacione.estado', fn ($q) => $q->where('bandeja_id', '!=', Bandeja::FINALIZADAS))
                ->selectRaw('users_id, count(*) as total')
                ->groupBy('users_id')
                ->pluck('total', 'users_id');

            return $ids->sortBy(fn ($id) => $cargas[$id] ?? 0)->first();
        }

        private function asignarEnBandeja(int $bandejaId, ?int $usuarioId): ?PostulacionAsignacion {
            $usuario = $usuarioId ? User::find($usuarioId) : null;

            if ($usuario === null) {
                return null;
            }

            $asignacion = PostulacionAsignacion::create([
                'postulacion_id' => $this->id,
                'bandeja_id' => $bandejaId,
                'estado_id' => $this->estado_id,
                'users_id' => $usuario->id,
                'nombre' => Str::limit($usuario->nombre_completo, 45, ''),
            ]);

            $this->setRelation('asignacion', $asignacion);

            return $asignacion;
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
                $asignacion = $this->asignarEnBandeja($this->estado->bandeja_id, $reclutador->id);

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
