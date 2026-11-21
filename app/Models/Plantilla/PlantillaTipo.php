<?php

namespace App\Models\Plantilla;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $nombre
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder<static>|PlantillaTipo newModelQuery()
 * @method static Builder<static>|PlantillaTipo newQuery()
 * @method static Builder<static>|PlantillaTipo onlyTrashed()
 * @method static Builder<static>|PlantillaTipo query()
 * @method static Builder<static>|PlantillaTipo whereCreatedAt($value)
 * @method static Builder<static>|PlantillaTipo whereDeletedAt($value)
 * @method static Builder<static>|PlantillaTipo whereId($value)
 * @method static Builder<static>|PlantillaTipo whereNombre($value)
 * @method static Builder<static>|PlantillaTipo whereUpdatedAt($value)
 * @method static Builder<static>|PlantillaTipo withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|PlantillaTipo withoutTrashed()
 * @mixin Eloquent
 */
class PlantillaTipo extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.plantillas_tipos';


    protected $fillable =
        [
    'nombre'
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
    'nombre' => 'nullable|string|max:510',
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
