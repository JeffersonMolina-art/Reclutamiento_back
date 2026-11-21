<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    class Evaluacion extends Model
    {

        use SoftDeletes;
        use HasFactory;

        protected $table = 'reclutamiento.evaluaciones';


        protected $fillable =
            [
                'postulacion_id',
                'entrevistador_id',
                'puntuacion',
                'modalidad',
                'fecha_evaluacion',
                'comentarios',
                'users_id',
                'postulacion_estado_id'
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
                'puntuacion' => 'float',
                'modalidad' => 'string',
                'fecha_evaluacion' => 'datetime',
                'comentarios' => 'string',
                'users_id' => 'integer',
                'postulacion_estado_id' => 'integer',
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
                'puntuacion' => 'required|numeric',
                'modalidad' => 'required|string|max:90',
                'fecha_evaluacion' => 'required|date',
                'comentarios' => 'nullable|string|max:510',
                'users_id' => 'required|integer',
                'postulacion_estado_id' => 'required|integer',
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

        public function postulacionEstado()
        {
            return $this->belongsTo(PostulacionEstado::class, 'postulacion_estado_id', 'id');
        }

        public function postulacione()
        {
            return $this->belongsTo(Postulacione::class, 'postulacion_id', 'id');
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'users_id', 'id');
        }

    }
