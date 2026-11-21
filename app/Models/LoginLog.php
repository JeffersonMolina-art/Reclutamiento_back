<?php

namespace App\Models;

use App\Models\User;
use App\Services\UserAgentParser;
use Carbon\CarbonInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * @property int $id
 * @property int|null $usuario_id
 * @property string|null $usuario_dominio
 * @property string $metodo
 * @property bool $two_factor_used
 * @property int|null $session_id
 * @property string|null $ip
 * @property string|null $user_agent
 * @property string|null $browser
 * @property string|null $browser_version
 * @property string|null $os
 * @property string|null $device_type
 * @property string|null $device_fingerprint
 * @property string|null $device_id
 * @property bool $is_new_device
 * @property string $resultado
 * @property string|null $motivo_fallo
 * @property string $severity_level
 * @property Carbon|null $logout_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $usuario
 * @method static Builder<static>|LoginLog newModelQuery()
 * @method static Builder<static>|LoginLog newQuery()
 * @method static Builder<static>|LoginLog query()
 * @method static Builder<static>|LoginLog whereBrowser($value)
 * @method static Builder<static>|LoginLog whereBrowserVersion($value)
 * @method static Builder<static>|LoginLog whereCreatedAt($value)
 * @method static Builder<static>|LoginLog whereDeviceFingerprint($value)
 * @method static Builder<static>|LoginLog whereDeviceId($value)
 * @method static Builder<static>|LoginLog whereDeviceType($value)
 * @method static Builder<static>|LoginLog whereId($value)
 * @method static Builder<static>|LoginLog whereIp($value)
 * @method static Builder<static>|LoginLog whereIsNewDevice($value)
 * @method static Builder<static>|LoginLog whereLogoutAt($value)
 * @method static Builder<static>|LoginLog whereMetodo($value)
 * @method static Builder<static>|LoginLog whereMotivoFallo($value)
 * @method static Builder<static>|LoginLog whereOs($value)
 * @method static Builder<static>|LoginLog whereResultado($value)
 * @method static Builder<static>|LoginLog whereSessionId($value)
 * @method static Builder<static>|LoginLog whereSeverityLevel($value)
 * @method static Builder<static>|LoginLog whereTwoFactorUsed($value)
 * @method static Builder<static>|LoginLog whereUpdatedAt($value)
 * @method static Builder<static>|LoginLog whereUserAgent($value)
 * @method static Builder<static>|LoginLog whereUsuarioDominio($value)
 * @method static Builder<static>|LoginLog whereUsuarioId($value)
 * @method static Builder<static>|LoginLog porUsuario(?int $usuarioId = null)
 * @method static Builder<static>|LoginLog ultimos30dias()
 * @mixin Eloquent
 */
class LoginLog extends Model
{
    protected $table = 'reclutamiento.login_logs';

    public $fillable = [
        'usuario_id',
        'usuario_dominio',
        'metodo',
        'two_factor_used',
        'session_id',
        'ip',
        'user_agent',
        'browser',
        'browser_version',
        'os',
        'device_type',
        'device_fingerprint',
        'device_id',
        'is_new_device',
        'resultado',
        'motivo_fallo',
        'severity_level',
        'logout_at',
    ];

    protected function casts(): array
    {
        return [
            'two_factor_used' => 'boolean',
            'is_new_device' => 'boolean',
            'logout_at' => 'datetime',
        ];
    }

    const METODO_CREDENCIALES = 'credenciales';
    const METODO_AZURE = 'azure';
    const METODO_2FA = '2fa';

    const RESULTADO_EXITOSO = 'exitoso';
    const RESULTADO_FALLIDO = 'fallido';

    const FALLO_BLOQUEADO = 'bloqueado';
    const FALLO_CREDENCIALES_INCORRECTAS = 'credenciales_incorrectas';
    const FALLO_USUARIO_INACTIVO = 'usuario_inactivo';
    const FALLO_USUARIO_NO_ENCONTRADO = 'usuario_no_encontrado';
    const FALLO_CODIGO_2FA_INVALIDO = 'codigo_2fa_invalido';

    const SEVERITY_INFO = 'info';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_CRITICAL = 'critical';

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function registrar(Request $request, array $datos): ?self
    {
        try {
            $ip = $request->header('x-real-ip') ?? $request->ip();
            $userAgent = $request->userAgent();
            $deviceId = $request->header('X-Device-Id');

            $fingerprint = $deviceId
                ? hash('sha256', $deviceId)
                : hash('sha256', $ip . '|' . $userAgent);

            $isNewDevice = false;
            if (!empty($datos['usuario_id'])) {
                $isNewDevice = !self::where('usuario_id', $datos['usuario_id'])
                    ->where('device_fingerprint', $fingerprint)
                    ->where('resultado', self::RESULTADO_EXITOSO)
                    ->exists();
            }

            $parsed = self::parsearUserAgent($userAgent);

            return self::create(array_merge([
                'ip' => $ip,
                'user_agent' => $userAgent,
                'browser' => $parsed['browser'],
                'browser_version' => $parsed['browser_version'],
                'os' => $parsed['os'],
                'device_type' => $parsed['device_type'],
                'device_id' => $deviceId,
                'device_fingerprint' => $fingerprint,
                'is_new_device' => $isNewDevice,
            ], $datos));
        } catch (Throwable $e) {
            manejarException($e);
            return null;
        }
    }

    private static function parsearUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return ['browser' => null, 'browser_version' => null, 'os' => null, 'device_type' => null];
        }

        $parser = new UserAgentParser($userAgent);

        return [
            'browser' => $parser->browser(),
            'browser_version' => $parser->browserVersion(),
            'os' => $parser->os(),
            'device_type' => $parser->deviceType(),
        ];
    }

    #[Scope]
    public function porUsuario(Builder $query, ?int $usuarioId = null): void
    {
        if ($usuarioId) {
            $user = User::find($usuarioId);
        }else{
            $user = auth()->user();
        }

        // 1. Validar que exista un usuario autenticado para evitar un error 500
        if (! $user) {
            $query->whereRaw('1 = 0'); // Fuerza a que no retorne resultados si no hay sesión
            return;
        }

        // 2. Agrupar la condición lógicamente para no romper otros wheres encadenados
        $query->where(function (Builder $q) use ($user) {
            $q->where('usuario_id', $user->id)
                ->orWhere('usuario_dominio', $user->usuario_dominio);
        });
    }

    #[Scope]
    public function ultimos30dias($query)
    {
        $query->where('created_at', '>=', now()->subDays(30));
    }

    protected function estaActiva(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => is_null($this->logout_at),
        );
    }

    protected function estadoSesion(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->esta_activa
                ? 'Activa'
                : 'Finalizada',
        );
    }

    protected function tiempoActivo(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if (!$this->created_at) {
                    return '-';
                }

                $fin = $this->logout_at ?? now();

                return $this->created_at->diffForHumans(
                    $fin,
                    [
                        'parts' => 2,
                        'short' => true,
                        'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                    ]
                );
            },
        );
    }
}
