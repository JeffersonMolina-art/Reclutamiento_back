<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\NotificacionesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{

    protected $table = 'reclutamiento.users';

    use HasFactory, hasApiTokens, InteractsWithMedia, SoftDeletes;
    use HasRoles,HasPermissions, NotificacionesTrait;



    const USUARIO_SISTEMA = '1';
    const autenticacionPorCredenciales = 'credenciales';
    const autenticacionPorAzure = 'azure';

    protected $guard_name = 'api';
    protected $primaryKey = 'id';


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'usuario_dominio',
        'nombre',
        'apellido',
        'email',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_habilitado',
        'two_factor_pending',
        'two_factor_confirmed_at',
        'estado',
        'puesto_id',
        'unidad_id',
        'region_id',
        'gerencia_id',
        'password_changed_at',
        'ultimo_login_el',
        'ultimo_login_ip',
        'ultima_autenticacion',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public static $rules =
        [
        'usuario_dominio' => 'required|string|max:191|unique:sqlsrv.reclutamiento.users,usuario_dominio',
        'email' => 'nullable|string|max:191',
        'nombre' => 'required|string|max:191',
        'apellido' => 'required|string|max:191',
        'password' => 'string|max:191',
        'estado' => 'nullable|in:Activo,Inactivo,Cancelado',
        'password_changed_at' => 'nullable|date',
        'remember_token' => 'nullable|string|max:100',
        'ultimo_login_el' => 'nullable|date',
        'ultimo_login_ip' => 'nullable|string|max:191',
        'puesto_id' => 'nullable|integer',
        'region_id' => 'nullable|integer',
        'gerencia_id' => 'nullable|integer',
        'unidad_id' => 'nullable|integer',
        ];

    /**
     * @var string[]
     * Reglas de validacion para el login
     */
    public static $ruleLogin = [
        'usuario_dominio' => 'required',
        'password' => 'required'
    ];

    public static $messages = [

    ];

    public static $atributosAdicionalesPorScope = [];


    protected $appends = ['nombre_completo'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'puesto_id' => 'integer',
            'unidad_id' => 'integer',
            'region_id' => 'integer',
            'gerencia_id' => 'integer',
            'password' => 'hashed',
            'two_factor_habilitado' => 'boolean',
        ];
    }

    /**
     * Verificar si el usuario es Super Administrador.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->roles->contains('name', 'Super Administrador'); // O verifica un campo booleano
    }

    public function scopeConAtributoAdicional($query, $atributoAdicionalNombre)
    {

        if (is_array($atributoAdicionalNombre)) {
            self::$atributosAdicionalesPorScope = array_merge(self::$atributosAdicionalesPorScope, $atributoAdicionalNombre);
            return $query;
        } else {
            self::$atributosAdicionalesPorScope[] = $atributoAdicionalNombre;
        }

        return $query;

    }

    public function scopeReclutadores($query)
    {
        return $query->where('estado', 'Activo')
            ->whereHas('roles', fn($rol) => $rol->where('name', Role::RECLUTADOR));
    }
    public function scopeAnalista($query)
    {
        return $query->where('estado', 'Activo')
            ->whereHas('roles', fn($rol) => $rol->where('name', Role::ANALISTA));
    }

    protected function getArrayableAppends(): array
    {
        if (count(self::$atributosAdicionalesPorScope) > 0) {
            $this->appends = array_merge($this->appends, self::$atributosAdicionalesPorScope);
            return $this->appends;
        }


        return parent::getArrayableAppends();
    }

    public function getNombreCompletoAttribute(): string
    {
        return "$this->nombre $this->apellido";
    }


    public function puesto(): BelongsTo
    {
        return $this->belongsTo(Puesto::class, 'puesto_id', 'id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'unidad_id', 'id');
    }

    public function gerencia(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Gerencia::class, 'gerencia_id', 'id');
    }

    public function preferencia(): HasOne
    {

        return $this->hasOne(UserPreferencia::class)->orderBy('created_at', 'desc');

    }

    /**
     * Get the user's permissions
     *
     * @return Collection
     */
    public function getAbilityRulesAttribute(): array
    {
        return $this->getAllPermissions()->map(function ($permission) {
            return [
                'action' => $permission->name,
                'subject' => $permission->subject,
            ];
        })->toArray();
    }

    /**
     * Get the user's permissions
     *
     * @return Collection
     */
    public function userResponse(): array
    {

        $user = $this->toArray();
        $user['puesto_nombre'] = optional($this->puesto)->nombre;
        $user['roles'] = $this->getRoleNames();
        $user['roles_id'] = $this->roles->pluck('id');
        $user['abilityRules'] = $this->getAbilityRulesAttribute();
        $user['tieneDobleFactorActivo'] = $this->tieneDobleFactorAuthActivo();
        $user['foto_perfil'] = $this->foto_perfil;

        return $user;
    }


    public function favoritos(): BelongsToMany
    {

        return $this->belongsToMany(MenuOpcion::class, 'reclutamiento.user_favoritos', 'user_id', 'opcion_id')->withPivot('orden');

    }

    public function tieneDobleFactorAuthActivo(): bool
    {
        return !empty($this->two_factor_secret) && !empty($this->two_factor_confirmed_at);
    }

    public function getFotoPerfilAttribute(): ?string
    {
        $media = $this->getMedia('foto_perfil')
            ->sortByDesc('created_at')
            ->first();

        return $media ? $media->getUrl() : null;
    }





}
