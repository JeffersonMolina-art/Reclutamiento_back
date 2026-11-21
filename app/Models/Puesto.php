<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * @property int $id
     * @property string $nombre
     * @property int|null $gerencia_id
     * @property string $estado
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Gerencia|null $gerencia
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto whereEstado($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto whereGerenciaId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto whereNombre($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto withTrashed(bool $withTrashed = true)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Puesto withoutTrashed()
     * @mixin \Eloquent
     */
    class Puesto extends Model
    {

        use SoftDeletes;
        use HasFactory;

        protected $table = 'reclutamiento.puestos';


        protected $fillable =
            [
                'nombre',
                'gerencia_id',
                'estado'
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
                'gerencia_id' => 'integer',
                'estado' => 'string',
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
                'nombre' => 'required|string|max:382',
                'gerencia_id' => 'nullable|integer',
                'estado' => 'required|string|max:510',
            ];


        /**
         * Custom messages for validation
         *
         * @var array
         */
        public static $messages = [

        ];

        protected $appends = ['nombre_gerencia'];

        public function getNombreGerenciaAttribute()
        {
            return $this->gerencia ? $this->gerencia->nombre : null;
        }

        /**
         * Accessor for relationships
         *
         * @var array
         */
        public function gerencia()
        {
            return $this->belongsTo(Gerencia::class, 'gerencia_id', 'id');
        }

    }
