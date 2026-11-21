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
 * @property int|null $departamento_id
 * @property int $existe_agencia
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Departamento|null $departamento
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereExisteAgencia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipio withoutTrashed()
 * @mixin \Eloquent
 */
class Municipio extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.municipios';


    protected $fillable =
        [
    'codigo',
    'nombre',
    'estado',
    'departamento_id',
    'existe_agencia'
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
        'departamento_id' => 'integer',
        'existe_agencia' => 'integer',
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
    'nombre' => 'required|string|max:510',
    'estado' => 'required|string|max:510',
    'departamento_id' => 'nullable|integer',
    'existe_agencia' => 'required|integer',
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
    public function departamento()
    {
    return $this->belongsTo(Departamento::class,'departamento_id','id');
    }

}
