<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $vacante_id
 * @property string $nombre
 * @property string|null $valor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Vacante|null $vacante
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica whereVacanteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica whereValor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteCaracteristica withoutTrashed()
 * @mixin \Eloquent
 */
class VacanteCaracteristica extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.vacante_caracteristicas';


    protected $fillable =
        [
    'vacante_id',
    'nombre',
    'valor'
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
        'valor' => 'string',
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
    'valor' => 'nullable|string|max:90',
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
