<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $candidato_id
 * @property string $nombre
 * @property string|null $titulo_carrera
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Candidato|null $candidato
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereCandidatoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereTituloCarrera($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoFormacion withoutTrashed()
 * @mixin \Eloquent
 */
class CandidatoFormacion extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.candidato_formacion';


    protected $fillable =
        [
    'candidato_id',
    'nombre',
    'titulo_carrera',
    'fecha_inicio',
    'fecha_fin'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'candidato_id' => 'integer',
        'nombre' => 'string',
        'titulo_carrera' => 'string',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
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
    'candidato_id' => 'required|integer',
    'nombre' => 'required|string|max:510',
    'titulo_carrera' => 'nullable|string|max:510',
    'fecha_inicio' => 'required|date',
    'fecha_fin' => 'nullable|date',
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
    public function candidato()
    {
    return $this->belongsTo(Candidato::class,'candidato_id','id');
    }

}
