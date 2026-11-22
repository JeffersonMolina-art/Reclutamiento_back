<?php

namespace App\Models;

use App\Traits\HasBitacora;
use App\Traits\HasFlujoPostulacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Postulacion extends Model implements HasMedia
{
    use HasFlujoPostulacion, HasBitacora, InteractsWithMedia;

    public const EXAMENES_MEDICOS = 'examenes_medicos';

    public const EXAMENES_TECNICOS = 'examenes_tecnicos';

    public const COLECCIONES_EXAMENES = [self::EXAMENES_MEDICOS, self::EXAMENES_TECNICOS];

    public function registerMediaCollections(): void
    {
        foreach (self::COLECCIONES_EXAMENES as $coleccion) {
            $this->addMediaCollection($coleccion);
        }
    }

    public function examenesMedicos(): Collection
    {
        return $this->getMedia(self::EXAMENES_MEDICOS);
    }

    public function examenesTecnicos(): Collection
    {
        return $this->getMedia(self::EXAMENES_TECNICOS);
    }

    public function archivos(?string $coleccion = null): array
    {
        $evaluaciones = $this->evaluaciones()->get()->keyBy('id');

        $listar = fn (string $nombre) => match ($nombre) {
            self::EXAMENES_MEDICOS => $this->examenesMedicos(),
            self::EXAMENES_TECNICOS => $this->examenesTecnicos(),
            default => $this->getMedia($nombre),
        };

        $resumir = fn (string $nombre) => $listar($nombre)
            ->map(fn ($media) => self::resumenMedia($media, $evaluaciones))
            ->values()
            ->all();

        if ($coleccion !== null) {
            return $resumir($coleccion);
        }

        return collect(self::COLECCIONES_EXAMENES)
            ->mapWithKeys(fn (string $nombre) => [$nombre => $resumir($nombre)])
            ->all();
    }

    public function guardarArchivo(UploadedFile $archivo, string $coleccion, User $usuario, Evaluacion $evaluacion): Media
    {
        $nombreArchivo = Str::studly($coleccion).'-'.$this->id.'-'.Str::random(8).'.'.$archivo->getClientOriginalExtension();

        $media = $this->addMedia($archivo)
            ->setFileName($nombreArchivo)
            ->usingName($coleccion)
            ->withCustomProperties([
                'nombre_original' => $archivo->getClientOriginalName(),
                'evaluacion_id' => $evaluacion->id,
            ])
            ->toMediaCollection($coleccion);

        $media->update([
            'gerencia_id' => $usuario->gerencia_id,
            'unidad_id' => $usuario->unidad_id,
            'region_id' => $usuario->region_id,
            'user_id' => $usuario->id,
        ]);

        return $media;
    }

    public static function resumenMedia($media, ?Collection $evaluaciones = null): array
    {
        $nombre = $media->getCustomProperty('nombre_original', $media->file_name);
        $evaluacion = $evaluaciones?->get($media->getCustomProperty('evaluacion_id'));

        return [
            'id' => $media->id,
            'name' => $nombre,
            'nombre' => $nombre,
            'coleccion' => $media->collection_name,
            'mime_type' => $media->mime_type,
            'original_url' => $media->getFullUrl(),
            'extension' => pathinfo($media->file_name, PATHINFO_EXTENSION),
            'tamano' => $media->size,
            'subido_el' => $media->created_at?->toIso8601String(),
            'evaluacion' => $evaluacion?->only([
                'id', 'puntuacion', 'modalidad', 'fecha_evaluacion', 'comentarios', 'entrevistador_id', 'postulacion_estado_id',
            ]),
        ];
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'postulacion_id');
    }

    public const ESTADO_INICIAL = 1;

    protected $table = 'reclutamiento.postulaciones';

    protected $fillable = [
        'vacante_id',
        'candidato_id',
        'estado_id',
        'fecha_postulacion',
        'fuente_reclutamiento',
    ];

    protected $casts = [
        'id' => 'integer',
        'vacante_id' => 'integer',
        'candidato_id' => 'integer',
        'estado_id' => 'integer',
        'fecha_postulacion' => 'date',
    ];

    public function scopeEnBandeja(Builder $query, string $codigo): Builder
    {
        return $query->whereHas('estado.bandeja', fn (Builder $q) => $q->where('codigo', $codigo));
    }
    public function scopeVisiblesPara(Builder $query, User $usuario): Builder
    {
        if ($usuario->hasAnyRole(Role::ROLES_ADMINISTRATIVOS)) {
            return $query;
        }

        return $query->whereHas('asignacion', fn (Builder $q) => $q->where('users_id', $usuario->id));
    }

    public function vacante(): BelongsTo
    {
        return $this->belongsTo(Vacante::class, 'vacante_id');
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(PostulacionEstado::class, 'estado_id');
    }

    public function asignacion(): HasOne
    {
        return $this->hasOne(PostulacionAsignacion::class, 'postulacion_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoPostulacion::class, 'postulacion_id');
    }
}
