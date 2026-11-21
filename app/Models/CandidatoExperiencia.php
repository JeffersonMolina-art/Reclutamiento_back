<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $candidato_id
 * @property string $empresa
 * @property string|null $puesto
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon|null $fecha_fin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Candidato|null $candidato
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia whereCandidatoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia whereEmpresa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia wherePuesto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoExperiencia withoutTrashed()
 * @mixin \Eloquent
 */
class CandidatoExperiencia extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.candidato_experiencias';


    protected $fillable =
        [
    'candidato_id',
    'empresa',
    'puesto',
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
        'empresa' => 'string',
        'puesto' => 'string',
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
    'empresa' => 'required|string|max:510',
    'puesto' => 'nullable|string|max:90',
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
