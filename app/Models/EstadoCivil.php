<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;

    /**
     *
     *
     * @property-read \App\Models\TFactory|null $use_factory
     * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil query()
     * @mixin \Eloquent
     */
    class EstadoCivil extends Model
    {


        protected $table = 'reclutamiento.estado_civils';


        protected $fillable =
            [
                'nombre',
                'estado',
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
                'estado' => 'string',
                'created_at' => 'timestamp',
                'updated_at' => 'timestamp',
            ];


        /**
         * Validation rules
         *
         * @var array
         */
        public static $rules =
            [
                'nombre' => 'required|string|max:191',
                'estado' => 'required|string',
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


    }
