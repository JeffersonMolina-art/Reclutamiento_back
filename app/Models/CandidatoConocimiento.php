<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $candidato_id
 * @property string $descripcion
 * @property string|null $nivel
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Candidato|null $candidato
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento whereCandidatoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento whereNivel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CandidatoConocimiento withoutTrashed()
 * @mixin \Eloquent
 */
class CandidatoConocimiento extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.candidato_conocimientos';


    protected $fillable =
        [
    'candidato_id',
    'descripcion',
    'nivel'
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
        'descripcion' => 'string',
        'nivel' => 'string',
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
    'descripcion' => 'required|string|max:510',
    'nivel' => 'nullable|string|max:90',
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
