<?php

    namespace App\Models\Plantilla;


    use App\Traits\HasBitacora;
    use App\Traits\HasScopedAppendsTrait;
    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Casts\Attribute;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Support\Carbon;
    use Spatie\MediaLibrary\HasMedia;
    use Spatie\MediaLibrary\InteractsWithMedia;

    /**
     * @property int $id
     * @property string $nombre
     * @property int|null $tipo_id
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Carbon|null $deleted_at
     * @method static Builder<static>|Plantilla newModelQuery()
     * @method static Builder<static>|Plantilla newQuery()
     * @method static Builder<static>|Plantilla onlyTrashed()
     * @method static Builder<static>|Plantilla query()
     * @method static Builder<static>|Plantilla whereCreatedAt($value)
     * @method static Builder<static>|Plantilla whereDeletedAt($value)
     * @method static Builder<static>|Plantilla whereId($value)
     * @method static Builder<static>|Plantilla whereIsActive($value)
     * @method static Builder<static>|Plantilla whereNombre($value)
     * @method static Builder<static>|Plantilla whereTipoId($value)
     * @method static Builder<static>|Plantilla whereUpdatedAt($value)
     * @method static Builder<static>|Plantilla withTrashed(bool $withTrashed = true)
     * @method static Builder<static>|Plantilla withoutTrashed()
     * @method static Builder<static>|Plantilla conAtributoAdicional($atributoAdicionalNombre)
     * @property-read Collection<int, DetallePlantilla> $detalles
     * @property-read int|null $detalles_count
     * @property-read DetallePlantilla|null $detalle_activo
     * @property-read string|null $tipo_plantilla
     * @property-read array|null $usuario_activo
     * @property-read string|null $usuario_create
     * @property-read PlantillaTipo|null $tipo
     * @property-read int|null $metodologias_count
     * @property-read array|null $opciones_activas
     * @property-read Collection<int, \App\Models\Bitacora> $bitacoras
     * @property-read int|null $bitacoras_count
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \App\Models\Media> $media
     * @property-read int|null $media_count
     * @property-read mixed $media_archivo
     * @mixin Eloquent
     */
    class Plantilla extends Model implements HasMedia
    {

        use SoftDeletes, HasBitacora, InteractsWithMedia, HasScopedAppendsTrait;

        const CONSENTIMIENTO = 1;
        const SOLICITUD_CREDITO = 2;
        const PAGARE = 3;
        const ORDEN_DESEMBOLSO = 4;
        const NO_COBERTURA = 5;

        protected $table = 'reclutamiento.plantillas';


        protected $fillable =
            [
                'nombre',
                'is_active',
                'tipo_id'
            ];


        /**
         * The attributes that should be casted to native types.
         *
         * @var array
         */
        protected $casts =
            [
                'id' => 'integer',
                'nombre' => 'string',
                'tipo_id' => 'integer',
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
                'deleted_at' => 'datetime',
            ];


        /**
         * Validation rules
         *
         * @var array
         */
        public static $rules =
            [
                'nombre' => 'required|string|max:510',
                'tipo_id' => 'nullable|integer',
            ];


        /**
         * Custom messages for validation
         *
         * @var array
         */
        public static $messages = [

        ];

        protected array $bitacoraEtiquetas = [
            'nombre' => 'Nombre',
            'is_active' => 'Activo',
            'tipo_id' => 'Tipo ID',
        ];

        protected array $bitacoraBooleanos = [
            'is_active' => ['Sí', 'No'],
        ];

        protected $hidden = ['media'];

        protected $appends = ['detalle_activo'];

        /**
         * Accessor for relationships
         *
         * @var array
         */
        public function tipo()
        {
            return $this->belongsTo(PlantillaTipo::class, 'tipo_id', 'id');
        }

        public function detalles()
        {
            return $this->hasMany(
                DetallePlantilla::class,
                'plantilla_id',
                'id'
            );
        }

        public function detalleActivo(): Attribute
        {
            return Attribute::make(
                get: fn () => $this->detalles()
                    ->where('is_active', true)
                    ->orderByDesc('version')
                    ->first(),
            );
        }


        public function usuarioActivo(): Attribute
        {
            return Attribute::make(
                get: function () {
                    $detalle = $this->detalles()
                        ->where('is_active', true)
                        ->orderByDesc('version')
                        ->with('userActive:id,nombre,apellido')
                        ->first();

                    if (!$detalle || !$detalle->userActive) {
                        return null;
                    }

                    return [
                        'nombre_completo' => trim(
                            $detalle->userActive->nombre . ' ' . $detalle->userActive->apellido
                        ),
                    ];
                }
            );
        }

        public function usuarioCreate(): Attribute
        {
            return Attribute::make(
                get: function () {
                    $detalle = $this->detalles()
                        ->where('is_active', true)
                        ->orderByDesc('version')
                        ->with('userCreate:id,nombre,apellido')
                        ->first();

                    if (!$detalle || !$detalle->userCreate) {
                        return null;
                    }

                    return trim(
                        $detalle->userCreate->nombre . ' ' . $detalle->userCreate->apellido
                    );
                }
            );
        }

        public function tipoPlantilla(): Attribute
        {
            return Attribute::make(
                get: fn () => $this->tipo ? $this->tipo->nombre : null,
            );
        }

        protected function opcionesActivas(): Attribute
        {
            return Attribute::make(
                get: fn () => $this->detalles()
                    ->where('is_active', true)
                    ->orderByDesc('version')
                    ->value('opciones'),
            );
        }

        public function registrerMediaCollections(): void
        {
            $this->addMediaCollection('plantillas');
        }
        public function mediaArchivo(): Attribute
        {
            return Attribute::make(
                get: fn() => [
                    'archivo_url' => $this->getFirstMediaUrl('plantillas'),
                    'id' => $this->getFirstMedia('plantillas')?->id,
                ],
            );
        }
    }
