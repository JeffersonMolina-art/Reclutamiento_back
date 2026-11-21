<?php

namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $codigo
 * @property string $nombre
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $jefe_id
 * @property-read User|null $user
 * @method static Builder<static>|Region newModelQuery()
 * @method static Builder<static>|Region newQuery()
 * @method static Builder<static>|Region query()
 * @method static Builder<static>|Region whereCodigo($value)
 * @method static Builder<static>|Region whereCreatedAt($value)
 * @method static Builder<static>|Region whereEstado($value)
 * @method static Builder<static>|Region whereId($value)
 * @method static Builder<static>|Region whereJefeId($value)
 * @method static Builder<static>|Region whereNombre($value)
 * @method static Builder<static>|Region whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Region extends Model
{


    use SoftDeletes;
    protected $table = 'reclutamiento.regiones';


    protected $fillable = [
        'codigo',
        'nombre',
        'estado',
        'jefe_id',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'codigo' => 'nullable|string|max:382',
        'nombre' => 'required|string|max:382',
        'estado' => 'required|string|max:510',
        'jefe_id' => 'nullable|integer',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

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
