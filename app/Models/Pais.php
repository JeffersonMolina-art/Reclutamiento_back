<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    /**
     *
     *
     * @property-read \App\Models\TFactory|null $use_factory
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais query()
     * @mixin \Eloquent
     */
    class Pais extends Model
    {


        use SoftDeletes;

        protected $table = 'reclutamiento.pais';

        protected $fillable =
            [
                'codigo',
                'nombre',
                'nacionalidad',
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
                'codigo' => 'string',
                'nombre' => 'string',
                'nacionalidad' => 'string',
                'estado' => 'string',
                'created_at' => 'timestamp',
                'updated_at' => 'timestamp',
                'deleted_at' => 'timestamp',
            ];


        /**
         * Validation rules
         *
         * @var array
         */
        public static $rules =
            [
                'codigo' => 'nullable|string|max:191',
                'codigo_t24' => 'nullable|string|max:191',
                'nombre' => 'required|string|max:191',
                'nacionalidad' => 'nullable|string|max:191',
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
