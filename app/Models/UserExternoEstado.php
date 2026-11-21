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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExternoEstado withoutTrashed()
 * @mixin \Eloquent
 */
class UserExternoEstado extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.usuario_externo_estados';


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
    'codigo' => 'required|string|max:60|unique:reclutamiento.usuario_externo_estados,codigo',
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
