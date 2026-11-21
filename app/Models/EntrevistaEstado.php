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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntrevistaEstado withoutTrashed()
 * @mixin \Eloquent
 */
class EntrevistaEstado extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.entrevistas_estado';


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
    'nombre' => 'required|string|max:510',
    'codigo' => 'required|string|max:60|unique:reclutamiento.entrevistas_estado,codigo',
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
