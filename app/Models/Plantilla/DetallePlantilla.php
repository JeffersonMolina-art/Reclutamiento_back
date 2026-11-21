<?php

namespace App\Models\Plantilla;


use App\Models\Media;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $plantilla_id
 * @property int|null $media_id
 * @property string $variables
 * @property string $opciones
 * @property int $version
 * @property bool $is_active
 * @property int|null $user_id
 * @property int|null $user_active_id
 * @property Carbon|null $fecha_activacion_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Plantilla $plantilla
 * @property-read User|null $userActive
 * @property-read User|null $userCreate
 * @method static Builder<static>|DetallePlantilla newModelQuery()
 * @method static Builder<static>|DetallePlantilla newQuery()
 * @method static Builder<static>|DetallePlantilla onlyTrashed()
 * @method static Builder<static>|DetallePlantilla query()
 * @method static Builder<static>|DetallePlantilla tieneVariable(string $variable)
 * @method static Builder<static>|DetallePlantilla whereCreatedAt($value)
 * @method static Builder<static>|DetallePlantilla whereDeletedAt($value)
 * @method static Builder<static>|DetallePlantilla whereFechaActivacionAt($value)
 * @method static Builder<static>|DetallePlantilla whereId($value)
 * @method static Builder<static>|DetallePlantilla whereIsActive($value)
 * @method static Builder<static>|DetallePlantilla whereMediaId($value)
 * @method static Builder<static>|DetallePlantilla wherePlantillaId($value)
 * @method static Builder<static>|DetallePlantilla whereUpdatedAt($value)
 * @method static Builder<static>|DetallePlantilla whereUserActiveId($value)
 * @method static Builder<static>|DetallePlantilla whereUserId($value)
 * @method static Builder<static>|DetallePlantilla whereVariables($value)
 * @method static Builder<static>|DetallePlantilla whereVersion($value)
 * @method static Builder<static>|DetallePlantilla withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|DetallePlantilla withoutTrashed()
 * @method static Builder<static>|DetallePlantilla whereOpciones($value)
 * @property string $path
 * @property-read Media|null $media
 * @method static Builder<static>|DetallePlantilla wherePath($value)
 * @mixin Eloquent
 */
class DetallePlantilla extends Model
{

    use SoftDeletes;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
        [
            'plantilla_id' => 'required|integer',
            'media_id' => 'nullable|integer',
            'variables' => 'required|string',
            'version' => 'required|integer',
            'is_active' => 'required|boolean',
            'user_id' => 'nullable|integer',
            'user_active_id' => 'nullable|integer',
            'fecha_activacion_at' => 'nullable|date',
        ];
    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];
    protected $table = 'reclutamiento.detalle_plantilla';
    protected $fillable =
        [
            'plantilla_id',
            'media_id',
            'variables',
            'version',
            'opciones',
            'is_active',
            'user_id',
            'user_active_id',
            'fecha_activacion_at'
        ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
            'id' => 'integer',
            'plantilla_id' => 'integer',
            'media_id' => 'integer',
            'variables' => 'string',
            'version' => 'integer',
            'opciones' => 'array',
            'is_active' => 'boolean',
            'user_id' => 'integer',
            'user_active_id' => 'integer',
            'fecha_activacion_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function plantilla()
    {
        return $this->belongsTo(Plantilla::class, 'plantilla_id', 'id');
    }

    public function userActive()
    {
        return $this->belongsTo(User::class, 'user_active_id', 'id');
    }

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class)
            ->withTrashed();
    }

    public function scopeTieneVariable($query, string $variable)
    {
        $var = strtoupper(trim($variable));

        return $query->whereRaw(
            "',' + variables + ',' LIKE ?",
            ['%,' . $var . ',%']
        );
    }


}
