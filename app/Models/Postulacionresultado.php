<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $motivo
 * @property \Illuminate\Support\Carbon $fecha_decision
 * @property int $postulacion_id
 * @property int $estado_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\PostulacionEstado|null $postulacionEstado
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado whereEstadoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado whereFechaDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado whereMotivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado wherePostulacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacionresultado withoutTrashed()
 * @mixin \Eloquent
 */
class Postulacionresultado extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.postulacion_resultados';


    protected $fillable =
        [
    'motivo',
    'fecha_decision',
    'postulacion_id',
    'estado_id',
    'user_id'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'motivo' => 'string',
        'fecha_decision' => 'datetime',
        'postulacion_id' => 'integer',
        'estado_id' => 'integer',
        'user_id' => 'integer',
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
    'motivo' => 'required|string|max:510',
    'fecha_decision' => 'required|date',
    'postulacion_id' => 'required|integer|unique:reclutamiento.postulacion_resultados,postulacion_id',
    'estado_id' => 'required|integer',
    'user_id' => 'required|integer',
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
    public function postulacionEstado()
    {
    return $this->belongsTo(PostulacionEstado::class,'estado_id','id');
    }

    public function postulacione()
    {
    return $this->belongsTo(Postulacione::class,'postulacion_id','id');
    }

    public function user()
    {
    return $this->belongsTo(User::class,'user_id','id');
    }

}
