<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * @property int $id
     * @property string $nombre
     * @property int $bandeja_id
     * @property string $codigo
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Bandeja|null $bandeja
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado whereBandejaId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado whereCodigo($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado whereNombre($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado withTrashed(bool $withTrashed = true)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionEstado withoutTrashed()
     * @mixin \Eloquent
     */
    class PostulacionEstado extends Model
    {

        use SoftDeletes;
        use HasFactory;

        public const RECIBIDA = 1;
        public const EN_REVISION = 2;
        public const ENTREVISTA = 3;
        public const OFERTA = 4;
        public const CONTRATADO = 5;
        public const DESCARTADA = 6;
        public const RECHAZO_OFERTA = 7;

        protected $table = 'reclutamiento.postulacion_estado';


        protected $fillable =
            [
                'nombre',
                'bandeja_id',
                'codigo'
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
                'bandeja_id' => 'integer',
                'codigo' => 'string',
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
                'nombre' => 'required|string|max:90',
                'bandeja_id' => 'required|integer',
                'codigo' => 'required|string|max:60|unique:sqlsrv.reclutamiento.postulacion_estado,codigo',
            ];

        public $appends = ['nombre_bandeja'];

        /**
         * Custom messages for validation
         *
         * @var array
         */
        public static $messages = [

        ];


        /**
         * Accessor for relationships
         *
         * @var array
         */
        public function bandeja()
        {
            return $this->belongsTo(Bandeja::class, 'bandeja_id', 'id');
        }

        public function getNombreBandejaAttribute()
        {
            return $this->bandeja ? $this->bandeja->nombre : null;
        }

    }
