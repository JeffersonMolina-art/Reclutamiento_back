<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string|null $codigo
 * @property string $nombre
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gerencia withoutTrashed()
 * @mixin \Eloquent
 */
class Gerencia extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.gerencias';


    protected $fillable =
        [
    'codigo',
    'nombre',
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
    public function user()
    {
        return $this->belongsTo(User::class, 'jefe_id', 'id');
    }

}
