<?php

    namespace App\Models;


    use Eloquent;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Support\Carbon;

    /**
     * @property int $id
     * @property string $nombre
     * @property int|null $gerencia_id
     * @property int|null $productividad
     * @property string $estado
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property-read Gerencia|null $gerencia
     * @method static Builder<static>|Puesto newModelQuery()
     * @method static Builder<static>|Puesto newQuery()
     * @method static Builder<static>|Puesto query()
     * @method static Builder<static>|Puesto whereCreatedAt($value)
     * @method static Builder<static>|Puesto whereEstado($value)
     * @method static Builder<static>|Puesto whereGerenciaId($value)
     * @method static Builder<static>|Puesto whereId($value)
     * @method static Builder<static>|Puesto whereNombre($value)
     * @method static Builder<static>|Puesto whereProductividad($value)
     * @method static Builder<static>|Puesto whereUpdatedAt($value)
     * @mixin Eloquent
     */
    class Puesto extends Model
    {

        use SoftDeletes;

        protected $table = 'reclutamiento.puestos';

        protected $fillable =
            [
                'nombre',
                'gerencia_id',
                'productividad',
                'estado'
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
                'productividad' => 'nullable|integer',
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
        public function gerencia()
        {
            return $this->belongsTo(Gerencia::class, 'gerencia_id', 'id');
        }

    }
