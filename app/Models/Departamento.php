<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int|null $codigo
 * @property string $nombre
 * @property string $estado
 * @property int|null $codigo_bankworks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCodigoBankworks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departamento withoutTrashed()
 * @mixin \Eloquent
 */
class Departamento extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.departamentos';


    protected $fillable =
        [
    'codigo',
    'nombre',
    'estado',
    'codigo_bankworks'
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
        'codigo_bankworks' => 'integer',
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
    'codigo' => 'nullable|integer',
    'nombre' => 'required|string|max:382',
    'estado' => 'required|string|max:510',
    'codigo_bankworks' => 'nullable|integer',
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
