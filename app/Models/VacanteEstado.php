<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $nombre
 * @property string $codigo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VacanteEstado withoutTrashed()
 * @mixin \Eloquent
 */
class VacanteEstado extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.vacantes_estados';


    protected $fillable =
        [
    'nombre',
    'codigo'
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
        'codigo' => 'string',
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
    'nombre' => 'required|string|max:90',
    'codigo' => 'required|string|max:60|unique:reclutamiento.vacantes_estados,codigo',
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
    

}
