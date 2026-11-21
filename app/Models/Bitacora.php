<?php

namespace App\Models;


use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $seccion
 * @property string $titulo
 * @property string $comentario
 * @property int $usuario_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $icono
 * @property string|null $color
 * @method static Builder<static>|Bitacora newModelQuery()
 * @method static Builder<static>|Bitacora newQuery()
 * @method static Builder<static>|Bitacora onlyTrashed()
 * @method static Builder<static>|Bitacora query()
 * @method static Builder<static>|Bitacora whereColor($value)
 * @method static Builder<static>|Bitacora whereComentario($value)
 * @method static Builder<static>|Bitacora whereCreatedAt($value)
 * @method static Builder<static>|Bitacora whereDeletedAt($value)
 * @method static Builder<static>|Bitacora whereIcono($value)
 * @method static Builder<static>|Bitacora whereId($value)
 * @method static Builder<static>|Bitacora whereModelId($value)
 * @method static Builder<static>|Bitacora whereModelType($value)
 * @method static Builder<static>|Bitacora whereSeccion($value)
 * @method static Builder<static>|Bitacora whereTitulo($value)
 * @method static Builder<static>|Bitacora whereUpdatedAt($value)
 * @method static Builder<static>|Bitacora whereUsuarioId($value)
 * @method static Builder<static>|Bitacora withTrashed()
 * @method static Builder<static>|Bitacora withoutTrashed()
 * @property-read mixed $es_usuario_sistema
 * @property-read mixed $fecha_crea
 * @property-read Model|Eloquent $model
 * @property-read User|null $usuario
 * @method static Builder<static>|Bitacora bitacoraGestor($modelType, $modelId)
 * @mixin Eloquent
 */
class Bitacora extends Model
{

    use SoftDeletes;

    protected $table = 'reclutamiento.bitacoras';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    protected $appends = ['fecha_crea','es_usuario_sistema'];

    public $fillable = [
        'model_type',
        'model_id',
        'seccion',
        'titulo',
        'comentario',
        'notificado',
        'usuario_id',
        'icono',
        'color'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'model_type' => 'string',
        'model_id' => 'integer',
        'seccion' => 'string',
        'titulo' => 'string',
        'comentario' => 'string',
        'usuario_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'icono' => 'string',
        'color' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'model_type' => 'required',
        'model_id' => 'required',
        'titulo' => 'required',
        'comentario' => 'required',
        'usuario_id' => 'required',
        'icono' => 'nullable|string|max:200',
        'color' => 'nullable|string|max:100',
    ];

    /**
     * @return BelongsTo
     **/
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function fechaCrea(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at->format('d/m/Y h:i:s a')
        );
    }
    public function esUsuarioSistema(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->usuario_id == User::USUARIO_SISTEMA
        );
    }
    public function scopeBitacoraGestor($query, $modelType, $modelId)
    {
        $ultimoId = Bitacora::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('seccion', 'JEFE DE AGENCIA')
            ->where('titulo', 'CREDITO APLICADO')
            ->max('id');

        if (!$ultimoId) {
            $query->whereRaw('1 = 0');
            return $query;
        }
        $query->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('id', '>=', $ultimoId);
        return $query;
    }
}
