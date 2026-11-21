<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracionTipo withoutTrashed()
 * @mixin \Eloquent
 */
class ImagenConfiguracionTipo extends Model
{

    use SoftDeletes;
    use HasFactory;

    const LOGO_CLARO = 1;
    const LOGO_OSCURO = 2;
    const FONDO_LOGIN_CLARO = 3;
    const FONDO_LOGIN_OSCURO = 4;


    protected $table = 'reclutamiento.imagen_configuracion_tipos';


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
        ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
        [
            'nombre' => 'required|string|max:510',
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



}
