<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;

class Candidato extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    public const COLECCION_CV = 'cv';

    public const CV_VIGENCIA_ANIOS = 1;

    protected $table = 'reclutamiento.candidato';

    protected $fillable = [
        'user_id',
        'dpi',
        'primer_nombre',
        'segundo_nombre',
        'tercer_nombre',
        'primer_apellido',
        'segundo_apellido',
        'correo_contacto',
        'telefono_contacto',
        'fecha_nacimiento',
        'expectativa_salarial',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'dpi' => 'string',
        'primer_nombre' => 'string',
        'segundo_nombre' => 'string',
        'tercer_nombre' => 'string',
        'primer_apellido' => 'string',
        'segundo_apellido' => 'string',
        'correo_contacto' => 'string',
        'telefono_contacto' => 'string',
        'fecha_nacimiento' => 'date',
        'expectativa_salarial' => 'decimal:2',
    ];

    protected $appends = [
        'nombre_completo',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COLECCION_CV)
            ->singleFile()
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class, 'candidato_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return collect([
            $this->primer_nombre,
            $this->segundo_nombre,
            $this->tercer_nombre,
            $this->primer_apellido,
            $this->segundo_apellido,
        ])->filter()->implode(' ');
    }

    public function conocimientos(): HasMany
    {
        return $this->hasMany(CandidatoConocimiento::class, 'candidato_id');
    }

    public function experiencias(): HasMany
    {
        return $this->hasMany(CandidatoExperiencia::class, 'candidato_id');
    }

    public function formaciones(): HasMany
    {
        return $this->hasMany(CandidatoFormacion::class, 'candidato_id');
    }

    /**
     * Requisitos del perfil para poder postularse a una vacante.
     */
    public function faltantesParaPostular(): array
    {
        $faltantes = [];

        if ((float) $this->expectativa_salarial <= 0) {
            $faltantes[] = 'expectativa salarial';
        }

        if (! $this->conocimientos()->exists()) {
            $faltantes[] = 'al menos un conocimiento';
        }

        if (! $this->experiencias()->exists()) {
            $faltantes[] = 'al menos una experiencia';
        }

        if (! $this->formaciones()->exists()) {
            $faltantes[] = 'al menos una formación';
        }

        return $faltantes;
    }

    public function cvActual(): ?MediaModel
    {
        return $this->getFirstMedia(self::COLECCION_CV);
    }

    public function cvVigente(): bool
    {
        $cv = $this->cvActual();

        return $cv !== null && $cv->created_at->gt(now()->subYears(self::CV_VIGENCIA_ANIOS));
    }
}
