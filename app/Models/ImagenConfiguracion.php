<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

/**
 *
 *
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ImagenConfiguracion withoutTrashed()
 * @mixin \Eloquent
 */
class ImagenConfiguracion extends Model implements HasMedia
{


    use HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, hasApiTokens,InteractsWithMedia, SoftDeletes;


    const LOGO =  'Logo';
    const ICONO = 'Icono';
    const FONDO_LOGIN = 'Fondo';


    protected $table = 'reclutamiento.imagen_configuracion';


    protected $fillable =
        [
            'nombre',
            'tipo_id'
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
            'nombre' => 'required|string|max:510',
        ];

    public function getMediaLogo()
    {
        // Aquí asumimos que el logo está asociado con una colección llamada 'logo'
        return $this->getFirstMedia('Fondo');
    }

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

    public function tipo() {

        return $this->belongsTo(ImagenConfiguracionTipo::class, 'tipo_id');

    }



}
