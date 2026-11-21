<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * @property int $id
     * @property string|null $codigo
     * @property string $nombre
     * @property string|null $emails
     * @property string $estado
     * @property int|null $regiones_id
     * @property int|null $gerencia_id
     * @property int|null $jefe_id
     * @property int|null $auxiliar_id
     * @property int|null $municipio_id
     * @property int|null $departamento_id
     * @property string|null $latitud
     * @property string|null $longitud
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Departamento|null $departamento
     * @property-read \App\Models\Gerencia|null $gerencia
     * @property-read \App\Models\Municipio|null $municipio
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereAuxiliarId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereCodigo($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereDepartamentoId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereEmails($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereEstado($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereGerenciaId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereJefeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereLatitud($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereLongitud($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereMunicipioId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereNombre($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereRegionesId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad withTrashed(bool $withTrashed = true)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad withoutTrashed()
     * @mixin \Eloquent
     */
    class Unidad extends Model
    {

        use SoftDeletes;
        use HasFactory;

        protected $table = 'reclutamiento.unidads';


        protected $fillable =
            [
                'codigo',
                'nombre',
                'emails',
                'estado',
                'regiones_id',
                'gerencia_id',
                'jefe_id',
                'auxiliar_id',
                'municipio_id',
                'departamento_id',
                'latitud',
                'longitud'
            ];


        /**
         * The attributes that should be casted to native types.
         *
         * @var array
         */
        protected $casts =
            [
                'id' => 'integer',
                'codigo' => 'string',
                'nombre' => 'string',
                'emails' => 'string',
                'estado' => 'string',
                'regiones_id' => 'integer',
                'gerencia_id' => 'integer',
                'jefe_id' => 'integer',
                'auxiliar_id' => 'integer',
                'municipio_id' => 'integer',
                'departamento_id' => 'integer',
                'latitud' => 'string',
                'longitud' => 'string',
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
                'codigo' => 'nullable|string|max:382',
                'nombre' => 'required|string|max:382',
                'emails' => 'nullable|string',
                'estado' => 'required|string|max:510',
                'regiones_id' => 'nullable|integer',
                'gerencia_id' => 'nullable|integer',
                'jefe_id' => 'nullable|integer',
                'auxiliar_id' => 'nullable|integer',
                'municipio_id' => 'nullable|integer',
                'departamento_id' => 'nullable|integer',
                'latitud' => 'nullable|string|max:382',
                'longitud' => 'nullable|string|max:382',
            ];


        /**
         * Custom messages for validation
         *
         * @var array
         */
        public static $messages = [

        ];

        protected $appends = ['nombre_departamento', 'nombre_gerencia', 'nombre_municipio', 'nombre_regiones'];
        /**
         * Accessor for relationships
         *
         * @var array
         */
        public function departamento()
        {
            return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
        }

        public function gerencia()
        {
            return $this->belongsTo(Gerencia::class, 'gerencia_id', 'id');
        }

        public function municipio()
        {
            return $this->belongsTo(Municipio::class, 'municipio_id', 'id');
        }

        public function region()
        {
            return $this->belongsTo(Region::class, 'regiones_id', 'id');
        }

        public function getNombreDepartamentoAttribute()
        {
            return $this->departamento ? $this->departamento->nombre : null;
        }
        public function getNombreGerenciaAttribute()
        {
            return $this->gerencia ? $this->gerencia->nombre : null;
        }
        public function getNombreMunicipioAttribute()
        {
            return $this->municipio ? $this->municipio->nombre : null;
        }
        public function getNombreRegionesAttribute()
        {
            return $this->region ? $this->region->nombre : null;
        }
    }
