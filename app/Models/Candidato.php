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

    protected $casts = ['fecha_nacimiento' => 'date'];

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
