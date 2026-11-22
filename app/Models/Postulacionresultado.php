<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string|null $motivo
 * @property \Illuminate\Support\Carbon|null $fecha_decision
 * @property int $postulacion_id
 * @property int $estado_id
 * @property int $user_id
 * @property string|null $salario_ofrecido
 * @property \Illuminate\Support\Carbon|null $fecha_inicio_propuesta
 * @property string|null $condiciones_oferta
 * @property string|null $estado_oferta
 * @property \Illuminate\Support\Carbon|null $fecha_respuesta_oferta
 * @property string|null $motivo_rechazo_oferta
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

    public const OFERTA_PENDIENTE = 'pendiente';
    public const OFERTA_ACEPTADA = 'aceptada';
    public const OFERTA_RECHAZADA = 'rechazada';

    protected $table = 'reclutamiento.postulacion_resultados';


    protected $fillable =
        [
    'motivo',
    'fecha_decision',
    'postulacion_id',
    'estado_id',
    'user_id',
    'salario_ofrecido',
    'fecha_inicio_propuesta',
    'condiciones_oferta',
    'estado_oferta',
    'fecha_respuesta_oferta',
    'motivo_rechazo_oferta',
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
        'salario_ofrecido' => 'decimal:2',
        'fecha_inicio_propuesta' => 'date',
        'condiciones_oferta' => 'string',
        'estado_oferta' => 'string',
        'fecha_respuesta_oferta' => 'datetime',
        'motivo_rechazo_oferta' => 'string',
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
    'motivo' => 'nullable|string|max:255',
    'fecha_decision' => 'nullable|date',
    'postulacion_id' => 'required|integer|unique:sqlsrv.reclutamiento.postulacion_resultados,postulacion_id',
    'estado_id' => 'required|integer',
    'user_id' => 'required|integer',
    'salario_ofrecido' => 'nullable|numeric|min:0',
    'fecha_inicio_propuesta' => 'nullable|date',
    'condiciones_oferta' => 'nullable|string',
    'estado_oferta' => 'nullable|string|max:20',
    'fecha_respuesta_oferta' => 'nullable|date',
    'motivo_rechazo_oferta' => 'nullable|string|max:255',
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

    public function postulacion()
    {
    return $this->belongsTo(Postulacion::class,'postulacion_id','id');
    }

    public function user()
    {
    return $this->belongsTo(User::class,'user_id','id');
    }

}
