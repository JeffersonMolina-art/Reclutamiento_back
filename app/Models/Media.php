<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

/**
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string|null $uuid
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string|null $mime_type
 * @property string $disk
 * @property string|null $conversions_disk
 * @property int $size
 * @property array<array-key, mixed> $manipulations
 * @property array<array-key, mixed> $custom_properties
 * @property array<array-key, mixed> $generated_conversions
 * @property array<array-key, mixed> $responsive_images
 * @property int|null $order_column
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $gerencia_id
 * @property int|null $region_id
 * @property int|null $unidad_id
 * @property int|null $user_id
 * @property int|null $asociado_id
 * @property int|null $clase_id
 * @property int|null $tipo_id
 * @property Carbon|null $deleted_at
 * @property int|null $correlativo
 * @property string|null $codigo
 * @property Carbon|null $fecha_documento
 * @property string|null $numero_documento
 * @property int|null $cif_asociado
 * @property int $suprimido
 * @property string|null $comentario_suprimido
 * @property int|null $user_suprimio_id
 * @property Carbon|null $fecha_suprimido
 * @property int|null $tipo_eliminacion_id
 * @property-read mixed $extension
 * @property-read mixed $human_readable_size
 * @property-read Model|Eloquent $model
 * @property-read mixed $original_url
 * @property-read mixed $preview_url
 * @property-read mixed $type
 * @property-read MediaClase|null $clase
 * @property-read MediaTipo|null $tipo
 * @method static MediaCollection<int, static> all($columns = ['*'])
 * @method static MediaCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|Media newModelQuery()
 * @method static Builder<static>|Media newQuery()
 * @method static Builder<static>|Media onlyTrashed()
 * @method static Builder<static>|Media ordered()
 * @method static Builder<static>|Media query()
 * @method static Builder<static>|Media whereAsociadoId($value)
 * @method static Builder<static>|Media whereClaseId($value)
 * @method static Builder<static>|Media whereCollectionName($value)
 * @method static Builder<static>|Media whereConversionsDisk($value)
 * @method static Builder<static>|Media whereCreatedAt($value)
 * @method static Builder<static>|Media whereCustomProperties($value)
 * @method static Builder<static>|Media whereDeletedAt($value)
 * @method static Builder<static>|Media whereDisk($value)
 * @method static Builder<static>|Media whereFileName($value)
 * @method static Builder<static>|Media whereGeneratedConversions($value)
 * @method static Builder<static>|Media whereGerenciaId($value)
 * @method static Builder<static>|Media whereId($value)
 * @method static Builder<static>|Media whereManipulations($value)
 * @method static Builder<static>|Media whereMimeType($value)
 * @method static Builder<static>|Media whereModelId($value)
 * @method static Builder<static>|Media whereModelType($value)
 * @method static Builder<static>|Media whereName($value)
 * @method static Builder<static>|Media whereOrderColumn($value)
 * @method static Builder<static>|Media whereRegionId($value)
 * @method static Builder<static>|Media whereResponsiveImages($value)
 * @method static Builder<static>|Media whereSize($value)
 * @method static Builder<static>|Media whereTipoId($value)
 * @method static Builder<static>|Media whereUnidadId($value)
 * @method static Builder<static>|Media whereUpdatedAt($value)
 * @method static Builder<static>|Media whereUserId($value)
 * @method static Builder<static>|Media whereUuid($value)
 * @method static Builder<static>|Media withTrashed()
 * @method static Builder<static>|Media withoutTrashed()
 * @method static Builder<static>|Media whereCifAsociado($value)
 * @method static Builder<static>|Media whereCodigo($value)
 * @method static Builder<static>|Media whereComentarioSuprimido($value)
 * @method static Builder<static>|Media whereCorrelativo($value)
 * @method static Builder<static>|Media whereFechaDocumento($value)
 * @method static Builder<static>|Media whereFechaSuprimido($value)
 * @method static Builder<static>|Media whereNumeroDocumento($value)
 * @method static Builder<static>|Media whereSuprimido($value)
 * @method static Builder<static>|Media whereTipoEliminacionId($value)
 * @method static Builder<static>|Media whereUserSuprimioId($value)
 * @mixin Eloquent
 */
class Media extends BaseMedia
{
    use softDeletes;

    protected $table = 'reclutamiento.media';

    protected $fillable = [
        'model_type',
        'model_id',
        'uuid',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
        'created_at',
        'updated_at',
        'gerencia_id',
        'region_id',
        'unidad_id',
        'user_id',
        'asociado_id',
        'clase_id',
        'tipo_id',
        'deleted_at',
        'correlativo',
        'codigo',
        'fecha_documento',
        'numero_documento',
        'cif_asociado',
        'suprimido',
        'comentario_suprimido',
        'user_suprimio_id',
        'fecha_suprimido',
        'tipo_eliminacion_id',
    ];



    public function clase(): BelongsTo
    {
        return $this->belongsTo(MediaClase::class, 'clase_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(MediaTipo::class, 'tipo_id');
    }

}
