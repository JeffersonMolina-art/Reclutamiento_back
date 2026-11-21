<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class Gerencia extends Model
    {

        use SoftDeletes;
        protected $table = 'reclutamiento.gerencias';


        protected $fillable =
            [
                'codigo',
                'nombre',
                'estado',
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
                'estado' => 'required|string|max:510',
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
        public function user()
        {
            return $this->belongsTo(User::class, 'jefe_id', 'id');
        }

    }
