<?php

namespace App\Models;


use App\Models\MenuOpcion;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $user_id
 * @property int $opcion_id
 * @property int|null $orden
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorito newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorito newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorito query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorito whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorito whereOpcionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorito whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorito whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserFavorito whereUserId($value)
 * @mixin \Eloquent
 */
class UserFavorito extends Model
{


    use HasFactory;

    protected $table = 'reclutamiento.user_favoritos';


    protected $fillable =
        [
            'user_id',
            'opcion_id',
            'orden'
        ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
            'user_id' => 'integer',
            'opcion_id' => 'integer',
            'orden' => 'integer',
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
            'user_id' => 'required|integer',
            'opcion_id' => 'required|integer',
            'orden' => 'nullable|integer',
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
    public function opcion()
    {
        return $this->belongsTo(MenuOpcion::class, 'opcion_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
