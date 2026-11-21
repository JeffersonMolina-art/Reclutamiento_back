<?php

    namespace App\Models;


    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * @property int $id
     * @property string $email
     * @property string|null $telefono
     * @property string $password
     * @property int $estado_id
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property \Illuminate\Support\Carbon|null $telefono_verified_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\UsuarioExternoEstado|null $usuarioExternoEstado
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereEmailVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereEstadoId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereTelefono($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereTelefonoVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno withTrashed(bool $withTrashed = true)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|UserExterno withoutTrashed()
     * @mixin \Eloquent
     */
    class UserExterno extends Model
    {

        use SoftDeletes;
        use HasFactory;

        protected $table = 'reclutamiento.user_externos';
        public const ESTADO_ACTIVO = 1;

        protected $fillable =
            [
                'email',
                'telefono',
                'password',
                'estado_id',
                'email_verified_at',
                'telefono_verified_at'
            ];


        /**
         * The attributes that should be casted to native types.
         *
         * @var array
         */
        protected $casts =
            [
                'id' => 'integer',
                'email' => 'string',
                'telefono' => 'string',
                'password' => 'string',
                'estado_id' => 'integer',
                'email_verified_at' => 'datetime',
                'telefono_verified_at' => 'datetime',
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
                'email' => 'required|string|max:510|unique:reclutamiento.user_externos,email',
                'telefono' => 'nullable|string|max:90',
                'password' => 'required|string|max:510',
                'estado_id' => 'required|integer',
                'email_verified_at' => 'nullable|date',
                'telefono_verified_at' => 'nullable|date',
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
        public function estado()
        {
            return $this->belongsTo(UsuarioExternoEstado::class, 'estado_id', 'id');
        }

        public function candidato(): HasOne
        {
            return $this->hasOne(Candidato::class, 'user_id');
        }

    }
