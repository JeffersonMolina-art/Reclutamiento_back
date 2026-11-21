<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    /**
     *
     *
     * @property-read \App\Models\Departamento|null $departamento
     * @property-read \App\Models\TFactory|null $use_factory
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio query()
     * @mixin \Eloquent
     */
    class Municipio extends Model
    {


        use SoftDeletes;
        protected $table = 'reclutamiento.municipios';


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
                'departamento_id' => 'integer',
                'deleted_at' => 'timestamp',
            ];


        protected $fillable =
            [
                'codigo',
                'nombre',
                'estado',
                'departamento_id',
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
                'departamento_id' => 'nullable|integer',
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
        public function departamento()
        {
            return $this->belongsTo(Departamento::class, 'departamento_id', 'id');
        }

    }
