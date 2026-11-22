<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * @property int $id
     * @property int $postulacion_id
     * @property int $entrevistador_id
     * @property int $estado_id
     * @property \Illuminate\Support\Carbon $fecha_hora_programada
     * @property string $modalidad
     * @property string $ubicacion_o_enlace
     * @property string|null $observaciones
     * @property string $tipo_entrevista
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\User|null $user
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereEntrevistadorId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereEstadoId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereFechaHoraProgramada($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereModalidad($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereObservaciones($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista wherePostulacionId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereTipoEntrevista($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereUbicacionOEnlace($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista withTrashed(bool $withTrashed = true)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Entrevista withoutTrashed()
     * @mixin \Eloquent
     */
    class Entrevista extends Model
    {

        use SoftDeletes;
        use HasFactory;

        protected $table = 'reclutamiento.entrevistas';


        protected $fillable =
            [
                'postulacion_id',
                'entrevistador_id',
                'estado_id',
                'fecha_hora_programada',
                'modalidad',
                'ubicacion_o_enlace',
                'observaciones',
                'tipo_entrevista'
            ];


        /**
         * The attributes that should be casted to native types.
         *
         * @var array
         */
        protected $casts =
            [
                'id' => 'integer',
                'postulacion_id' => 'integer',
                'entrevistador_id' => 'integer',
                'estado_id' => 'integer',
                'fecha_hora_programada' => 'datetime',
                'modalidad' => 'string',
                'ubicacion_o_enlace' => 'string',
                'observaciones' => 'string',
                'tipo_entrevista' => 'string',
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
                'postulacion_id' => 'required|integer',
                'entrevistador_id' => 'required|integer',
                'estado_id' => 'required|integer',
                'fecha_hora_programada' => 'required|date',
                'modalidad' => 'required|string|max:90',
                'ubicacion_o_enlace' => 'required|string|max:1000',
                'observaciones' => 'nullable|string|max:510',
                'tipo_entrevista' => 'required|string|max:510',
            ];


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
        public function entrevistador()
        {
            return $this->belongsTo(User::class, 'entrevistador_id', 'id');
        }

        public function estado()
        {
            return $this->belongsTo(EntrevistaEstado::class, 'estado_id', 'id');
        }

        public function postulacion()
        {
            return $this->belongsTo(Postulacion::class, 'postulacion_id', 'id');
        }

    }
