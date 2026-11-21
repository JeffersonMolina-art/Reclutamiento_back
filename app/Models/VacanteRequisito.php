<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $vacante_id
 * @property string $nombre
 * @property bool $es_obligatorio
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Vacante|null $vacante
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito whereEsObligatorio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito whereVacanteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteRequisito withoutTrashed()
 * @mixin \Eloquent
 */
class VacanteRequisito extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.vacante_requisitos';


    protected $fillable =
        [
    'vacante_id',
    'nombre',
    'es_obligatorio'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'vacante_id' => 'integer',
        'nombre' => 'string',
        'es_obligatorio' => 'boolean',
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
    'vacante_id' => 'required|integer',
    'nombre' => 'required|string|max:510',
    'es_obligatorio' => 'required|boolean',
];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function vacante()
    {
    return $this->belongsTo(Vacante::class,'vacante_id','id');
    }

}
