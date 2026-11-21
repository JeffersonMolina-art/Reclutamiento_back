<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
 use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\TipoDocumento
 *
 * @property int $id
 * @property int $codigo
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento query()
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TipoDocumento withoutTrashed()
 * @mixin \Eloquent
 */
class TipoDocumento extends Model
{
    use SoftDeletes;

    public $table = 'reclutamiento.tipo_documentos';

    public $fillable = [
        'codigo',
        'nombre'
    ];

    protected $casts = [
        'nombre' => 'string'
    ];

    public static array $rules = [
        'codigo' => 'required',
        'nombre' => 'required|string|max:255',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public static array $messages = [

    ];

    const ESCRITURA_PUBLICA = 1;
    const DOCUMENTO_PRIVADO = 2;
    const LETRA_DE_CAMBIO = 3;
    const PAGARE = 4;
    const FACTURA_CAMBIARIA = 5;
    const BONO_DE_PRENDA = 6;
    const OTROS = 7;




}
