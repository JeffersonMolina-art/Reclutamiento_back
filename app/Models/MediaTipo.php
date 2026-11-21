<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class MediaTipo
 *
 * @package App\Models
 * @property int $id
 * @property string $nombre
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, MediaClase> $mediaClases
 * @property-read int|null $media_clases_count
 * @method static Builder<static>|MediaTipo newModelQuery()
 * @method static Builder<static>|MediaTipo newQuery()
 * @method static Builder<static>|MediaTipo query()
 * @method static Builder<static>|MediaTipo whereCreatedAt($value)
 * @method static Builder<static>|MediaTipo whereDeletedAt($value)
 * @method static Builder<static>|MediaTipo whereId($value)
 * @method static Builder<static>|MediaTipo whereNombre($value)
 * @method static Builder<static>|MediaTipo whereUpdatedAt($value)
 * @mixin Eloquent
 */
class MediaTipo extends Model
{
    protected $table = 'reclutamiento.media_tipos';

    const GESTION_CREDITOS = 1;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'nombre'
    ];

    protected $casts = [
        'id' => 'integer',
        'nombre' => 'string'
    ];

    public static $rules = [
        'nombre' => 'required'
    ];

    public function mediaClases(): BelongsToMany
    {
        return $this->belongsToMany(MediaClase::class, 'reclutamiento.media_clase_tipo');
    }
}
