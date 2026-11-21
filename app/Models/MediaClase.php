<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class MediaClase
 *
 * @package App\Models
 * @property int $id
 * @property string $nombre
 * @property int|null $meses_vigencias
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection<int, MediaTipo> $mediaTipos
 * @property-read int|null $media_tipos_count
 * @method static Builder<static>|MediaClase newModelQuery()
 * @method static Builder<static>|MediaClase newQuery()
 * @method static Builder<static>|MediaClase query()
 * @method static Builder<static>|MediaClase whereCreatedAt($value)
 * @method static Builder<static>|MediaClase whereDeletedAt($value)
 * @method static Builder<static>|MediaClase whereId($value)
 * @method static Builder<static>|MediaClase whereMesesVigencias($value)
 * @method static Builder<static>|MediaClase whereNombre($value)
 * @method static Builder<static>|MediaClase whereUpdatedAt($value)
 * @mixin Eloquent
 */
class MediaClase extends Model
{
    protected $table = 'reclutamiento.media_clases';

    const SOLICITUD_CREDITO = 1;
    const PAGARE = 2;
    const ORDEN_DESEMBOLSO = 3;
    const ANEXO2_CONSENTIMIENTO = 4;
    const CONTRATO_5B = 5;
    const DOCS_PROYECTOS = 6;
    const CONSENTIMIENTO = 7;
    const COTIZACION = 8;
    const TARJETA_CREDITO = 9;
    const COMPRA_VENTA = 10;
    const PLANILLA = 11;
    const PROMESA_COMPRA_VENTA = 12;
    const PROMESA_VENTA = 13;
    const COTIZACION_VEHICULOS = 14;
    const RECONOCIMIENTO_DEUDA = 15;


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'nombre',
        'meses_vigencias'
    ];

    protected $casts = [
        'id' => 'integer',
        'nombre' => 'string',
        'meses_vigencias' => 'integer'
    ];

    public static $rules = [
        'nombre' => 'required'
    ];

    public function mediaTipos(): BelongsToMany
    {
        return $this->belongsToMany(MediaTipo::class, 'reclutamiento.media_clase_tipo');
    }
}
