<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    /**
     *
     *
     * @property-read \App\Models\TFactory|null $use_factory
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento query()
     * @mixin \Eloquent
     */
    class Departamento extends Model
    {


        use SoftDeletes;

        protected $table = 'reclutamiento.departamentos';


        protected $fillable =
            [
                'codigo',
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
                'codigo' => 'integer',
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
                'codigo' => 'nullable|integer',
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
