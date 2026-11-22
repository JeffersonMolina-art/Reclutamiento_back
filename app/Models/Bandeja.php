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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bandeja withoutTrashed()
 * @mixin \Eloquent
 */
class Bandeja extends Model
{

    use SoftDeletes;
    use HasFactory;

    public const RECIBIDAS = 1;
    public const EN_PROCESO = 2;
    public const FINALIZADAS = 3;

    protected $table = 'reclutamiento.bandeja';


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
    'codigo' => 'required|string|max:60|unique:sqlsrv.reclutamiento.bandeja,codigo',
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
