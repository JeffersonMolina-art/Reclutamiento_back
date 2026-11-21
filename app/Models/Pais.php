<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string|null $codigo
 * @property string $nombre
 * @property string|null $nacionalidad
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais whereNacionalidad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pais withoutTrashed()
 * @mixin \Eloquent
 */
class Pais extends Model
{

    use SoftDeletes;
    use HasFactory;

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
    'codigo' => 'nullable|string|max:382',
    'nombre' => 'required|string|max:382',
    'nacionalidad' => 'nullable|string|max:382',
    'estado' => 'required|string|max:510',
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
