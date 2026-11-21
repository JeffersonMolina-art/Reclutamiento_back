<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;

    use Illuminate\Database\Eloquent\Factories\HasFactory;

    class SeguimientoPostulacion extends Model
    {


        use HasFactory;

        protected $table = 'reclutamiento.seguimiento_postulacion';


        protected $fillable =
            [
                'comentario',
                'porcentaje',
                'postulacion_id',
                'estado_id',
                'user_id',
                'puntaje_fase',
                'estado_anterior_id',
                'fecha_cambio'
            ];


        /**
         * The attributes that should be casted to native types.
         *
         * @var array
         */
        protected $casts =
            [
                'id' => 'integer',
                'comentario' => 'string',
                'porcentaje' => 'float',
                'postulacion_id' => 'integer',
                'estado_id' => 'integer',
                'user_id' => 'integer',
                'puntaje_fase' => 'float',
                'estado_anterior_id' => 'integer',
                'fecha_cambio' => 'datetime',
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
            ];


        /**
         * Validation rules
         *
         * @var array
         */
        public static $rules =
            [
                'comentario' => 'required|string|max:510',
                'porcentaje' => 'required|numeric',
                'postulacion_id' => 'required|integer',
                'estado_id' => 'required|integer',
                'user_id' => 'nullable|integer',
                'puntaje_fase' => 'nullable|numeric',
                'estado_anterior_id' => 'nullable|integer',
                'fecha_cambio' => 'required|date',
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
        public function estadoAnterior()
        {
            return $this->belongsTo(PostulacionEstado::class, 'estado_anterior_id', 'id');
        }

        public function estadoActual()
        {
            return $this->belongsTo(PostulacionEstado::class, 'estado_id', 'id');
        }

        public function postulacione()
        {
            return $this->belongsTo(Postulacion::class, 'postulacion_id', 'id');
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'user_id', 'id');
        }

    }
