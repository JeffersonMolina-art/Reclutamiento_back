<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Relations\HasOne;

    /**
     * @property int $id
     * @property int $bandeja_id
     * @property int $postulacion_id
     * @property int $users_id
     * @property string $nombre
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Bandeja|null $bandeja
     * @property-read \App\Models\User|null $user
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion whereBandejaId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion whereNombre($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion wherePostulacionId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PostulacionAsignacion whereUsersId($value)
     * @mixin \Eloquent
     */
    class PostulacionAsignacion extends Model
    {


        use HasFactory;

        protected $table = 'reclutamiento.postulacion_asignaciones';


        protected $fillable =
            [
                'bandeja_id',
                'postulacion_id',
                'users_id',
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
                'bandeja_id' => 'integer',
                'postulacion_id' => 'integer',
                'users_id' => 'integer',
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
                'bandeja_id' => 'required|integer|unique:reclutamiento.postulacion_asignaciones,bandeja_id',
                'postulacion_id' => 'required|integer|unique:reclutamiento.postulacion_asignaciones,postulacion_id',
                'users_id' => 'required|integer',
                'nombre' => 'required|string|max:90',
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
        public function bandeja()
        {
            return $this->belongsTo(Bandeja::class, 'bandeja_id', 'id');
        }

        public function postulacione()
        {
            return $this->belongsTo(Postulacione::class, 'postulacion_id', 'id');
        }

        public function user()
        {
            return $this->belongsTo(User::class, 'users_id', 'id');
        }

        public function asignacion(): HasOne
        {
            return $this->hasOne(PostulacionAsignacion::class, 'postulacion_id');
        }

        public function scopeVisiblesPara(Builder $query, User $usuario): Builder
        {
            if ($usuario->can('ver todas las postulaciones')) {
                return $query;
            }

            return $query->whereHas('asignacion', function (Builder $asignacion) use ($usuario) {
                $asignacion->where('users_id', $usuario->getKey());
            });
        }

        public function scopeEnBandeja(Builder $query, string $codigo): Builder
        {
            return $query->whereHas('estado.bandeja', function (Builder $bandeja) use ($codigo) {
                $bandeja->where('codigo', $codigo);
            });
        }

    }
