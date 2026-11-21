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
 * @property string $valor
 * @property bool $cifrado
 * @property string $tipo
 * @property string $grupo
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereCifrado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereGrupo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereTipo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereValor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion withoutTrashed()
 * @mixin \Eloquent
 */
class Configuracion extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'reclutamiento.configuracions';


    protected $fillable = [
    'nombre',
    'valor',
    'cifrado',
    'tipo',
    'grupo',
    'descripcion'
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
        'valor' => 'string',
        'cifrado' => 'boolean',
        'tipo' => 'string',
        'grupo' => 'string',
        'descripcion' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
        'deleted_at' => 'string',
    ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
    [
    'nombre' => 'required|string|max:510',
    'valor' => 'required|string',
    'cifrado' => 'required|boolean',
    'tipo' => 'required|string|max:510',
    'grupo' => 'required|string',
    'descripcion' => 'required|string',
];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];

    const ROLES_PANEL_DRC = 'Roles Panel DRC';


    /**
     * Accessor for relationships
     *
     * @var array
     */


    public static function obtenerConfiguracionesFormateadas($grupo): array
    {
        $configuraciones = self::where('grupo', $grupo)->get();
        $formateado = [];

        foreach ($configuraciones as $config) {
            // Se usa el campo 'nombre' como key y 'valor' como value
            $formateado[$config->nombre] = $config->valor;
        }

        return $formateado;
    }

}
