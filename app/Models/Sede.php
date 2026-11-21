<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sede withoutTrashed()
 * @mixin \Eloquent
 */
class Sede extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.sede';


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
    'nombre' => 'required|string|max:90',
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
