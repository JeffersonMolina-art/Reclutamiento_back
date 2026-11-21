<?php

use App\Extenciones\MontoALetras;
use App\Extenciones\NumeroALetras;
use App\Models\Configuracion;
use App\Models\SIGUA\Media;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

function parseStrLike($string = null): string|null
{
    if (!$string) {
        return null;
    }

    $search = str_replace(" ", "%", $string);
    return "%" . $search . "%";
}

function usuarioAutenticado(): User|Authenticatable|null
{
    return auth()->user() ?? auth('api')->user();
}

function base64toFile($base64,$fileName): string
{
    $file = base64_decode($base64);

    Storage::disk('local_temp')->put($fileName, $file);

    return storage_path('app/temp/'.$fileName);
}

function getLogo($conversion = 'webp'): string
{
    return asset('img/logo-guayacan.png');
}

function configuracion($grupo,$nombre){
    return Configuracion::where('nombre',$nombre)->where('grupo',$grupo)->first();
}

function cifrar(string $dato): string
{
    $metodo = 'AES-256-CBC';
    $clave_secreta = env('CLAVE_ENCRIPTACION_CONFIG');
    $iv = random_bytes(openssl_cipher_iv_length($metodo));
    $cifrado = openssl_encrypt($dato, $metodo, $clave_secreta, 0, $iv);
    return base64_encode($iv . '::' . $cifrado);
}

/**
 * @param string $dato_cifrado
 * @return string
 */
function descifrar(string $dato_cifrado): string
{
    $metodo = 'AES-256-CBC';
    $clave_secreta = env('CLAVE_ENCRIPTACION_CONFIG');
    list($iv, $cifrado) = explode('::', base64_decode($dato_cifrado));
    return openssl_decrypt($cifrado, $metodo, $clave_secreta, 0, $iv);
}

function aMayusculas(string $str): string
{
    return mb_strtoupper($str,'UTF-8');
}

/*
 * Función para obtener el correlativo de un documento
 */
function prefijoCeros($numero,$cantidadCeros){
    return str_pad($numero,$cantidadCeros,"0",STR_PAD_LEFT);
}

/*
 * Función para verificar si el entorno es de producción
 */
function manejarException(Throwable $exception): string
{
    try {
        /**
         * @var User $user
         */
        $user = auth()->user() ?? auth('api')->user() ?? new User();

        Log::error($exception->getMessage(), [
            'archivo' => $exception->getFile() . '  Linea: ' . $exception->getLine(),
            'request' => request()->all(),
            'usuario' => array_merge(
                $user->toArray(),
                [
                    'foto_perfil' => $user->foto_perfil ?? null,
                    'roles' => $user->roles?->pluck('name')->toArray() ?? [],
                    'puesto' => $user->puesto?->nombre,
                    'unidad' => $user->unidad?->nombre,
                ]
            ),
        ]);

    } catch (Throwable $e) {
        Log::error($e->getMessage(), [
            'archivo' => $e->getFile() . '  Linea: ' . $e->getLine(),
            'request' => request()->all(),
            'usuario' => array_merge(
                $user->toArray(),
                [
                    'foto_perfil' => $user->foto_perfil ?? null,
                    'roles' => $user->roles?->pluck('name')->toArray() ?? [],
                    'puesto' => $user->puesto?->nombre,
                    'unidad' => $user->unidad?->nombre,
                ]
            ),
        ]);
    }
    return $user->isSuperAdmin() ? $exception->getMessage() : 'Hubo un error intente de nuevo';
}

/*
 * Funcion para manejar reberb si está activo
 */
function manejarReberb($evento)
{
    try {
        $reverbActivo = Cache::remember('RevertStart', now()->addDays(1), function () {
            return Configuracion::where([['nombre', 'ReverbActivo'], ['grupo', 'Reverb']])
                ->pluck('valor')->first();
        });
        if ($reverbActivo) {
            event($evento);
        }
    }catch (Throwable $e) {
        manejarException($e);
    }
}

/*
 * Función para formatear un número a un formato específico
 */
function nfp($numero=null){

    if (is_null($numero)){
        return null;
    }

    $decimales = $numero>0 ? 2 : 0;

    return number_format($numero,$decimales,'.',',');
}

/*
 * Función para convertir un monto a letras
 */
function montoAletras($monto){
    $monto = str_replace(',','',$monto);
    $converitdor = new MontoALetras();
    $textoenletras = $converitdor->convertir($monto);
    $textoenletras = str_replace('  ',' ', $textoenletras);
    $textoenletras = trim($textoenletras);


    return $textoenletras;

}

function numAletras($numero,$moneda=null,$centimos=null,$puntodecimal = false){
    return NumeroALetras::convertir($numero,$moneda,$centimos, false, $puntodecimal);
}


function meses(){
    return [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

}

function mesLetras($mes=null){
    $meses = meses();

    return strtoupper($meses[intval($mes)]);
}


function fechaALetras($fecha){
    [$dia,$mes,$anio]=explode("/",$fecha);

    if($dia == 1){
        $textoDia = 'UNO';
    }else{
        $textoDia = numAletras($dia);
    }

    return $textoDia." DE ".mesLetras($mes)." DEL ".numAletras($anio);
}

function fechaMixta(string $fecha): string
{
    [$dia, $mes, $anio] = explode('/', $fecha);

    $mesTexto = mb_strtolower(meses()[(int)$mes], 'UTF-8');

    return intval($dia).' de '.$mesTexto.' del año '.$anio;
}

function fechaActual($fomato='es'){
    if ($fomato=='en'){
        return Carbon::now()->format('Y-m-d');
    }

    return Carbon::now()->format('d/m/Y');
}

function fechaActualLetras(){
    [$dia,$mes,$anio]=explode("/",fechaActual());

    return numAletras($dia)." DE ".mesLetras($mes)." DEL ".numAletras($anio);
}

function numeroCuentaFenaforeALetras($numero)
{
    // Convertimos el número a string con padding de 15 dígitos (mantiene ceros a la izquierda)
    $numeroStr = str_pad((string)$numero, 15, '0', STR_PAD_LEFT);

    // Verificamos si el número original tiene UN solo cero al inicio (como '010...')
    $agregarCeroTexto = (strlen($numero) > 1 && $numero[0] === '0');

    // Dividimos en bloques
    $bloque1 = substr($numeroStr, 0, 3);  // Billones
    $bloque2 = substr($numeroStr, 3, 6);  // Millones
    $bloque3 = substr($numeroStr, 9, 6);  // Miles

    $convertidor = new MontoALetras();
    $texto = [];

    if ($agregarCeroTexto) {
        $texto[] = 'CERO';
    }

    if ((int)$bloque1 > 0) {
        $texto[] = $convertidor->convertir((int)$bloque1, [
            'plural' => 'BILLONES',
            'singular' => 'BILLON',
            'centPlural' => '',
            'centSingular' => ''
        ]);
    }

    if ((int)$bloque2 > 0) {
        $texto[] = $convertidor->convertir((int)$bloque2, [
            'plural' => 'MILLONES',
            'singular' => 'MILLON',
            'centPlural' => '',
            'centSingular' => ''
        ]);
    }

    if ((int)$bloque3 > 0) {
        $texto[] = $convertidor->convertir((int)$bloque3, [
            'plural' => '',
            'singular' => '',
            'centPlural' => '',
            'centSingular' => ''
        ]);
    }

    return strtoupper(trim(preg_replace('/\s+/', ' ', implode(' ', $texto))));
}

function dpiConEspacios($dpi){
    $cui1 = substr($dpi,0,4);
    $cui2 = substr($dpi,4,5);
    $cui3 = substr($dpi,9,4);

    return $cui1." ".$cui2." ".$cui3;
}

function dpiAletras($dpi){

    [$cui1,$cui2,$cui3] = explode(" ",dpiConEspacios($dpi));

    $cui1Letras = trim(NumeroALetras::convertir($cui1));
    $cui2Letras = trim(NumeroALetras::convertir($cui2));
    $cui3Letras = trim(NumeroALetras::convertir($cui3));

    $cui2Letras = substr($cui2,0,1)==0 ? "CERO ".$cui2Letras : $cui2Letras;
    $cui3Letras = substr($cui3,0,1)==0 ? "CERO ".$cui3Letras : $cui3Letras;

    return $cui1Letras." ESPACIO ".$cui2Letras." ESPACIO ".$cui3Letras;
}


function entornoLocal(): bool
{
    return app()->environment() == 'local';
}

function entornoDesarrollo()
{
    return app()->environment() == 'dev';
}
function entornoProduccion()
{
    return app()->environment() == 'prod';
}


function mediaSigua(Media $media): string
{
    if (entornoLocal()) {
        $baseUrl = env('URL_SIGUA'). '/storage';
//        $baseUrl = 'http://sigua.local/storage';
        return "{$baseUrl}/{$media->id}/{$media->file_name}";
    }

    $baseUrl = Cache::remember(
        'AZURE_BLOB_URL_WITH_SAS',
        now()->addDay(),
        fn () => configuracion('Sigua', 'AzureSiguaURL')->valor
    );

    $sasToken = Cache::remember(
        'AZURE_BLOB_SAS_TOKEN',
        now()->addDay(),
        fn () => configuracion('Sigua', 'AzureSiguaToken')->valor
    );

    return rtrim($baseUrl, '/') . '/' . $media->id . '/' . ltrim($media->file_name, '/') . $sasToken;
}


function numeroCuenta($num){
    if (strlen($num)==9){
        return prefijoCeros($num,10);
    }

    return $num;
}

function haceCuantoFue($fechaRegistro): string
{
    try {
        $fecha = $fechaRegistro instanceof \DateTime ? $fechaRegistro : new \DateTime($fechaRegistro);
    } catch (\Exception $e) {
        return 'Fecha inválida';
    }

    $ahora = new \DateTime();
    $diff = $ahora->diff($fecha);

    $partes = [];

    if ($diff->y > 0) $partes[] = $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
    if ($diff->m > 0) $partes[] = $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
    if ($diff->d > 0) $partes[] = $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
    if ($diff->h > 0) $partes[] = $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
    if ($diff->i > 0) $partes[] = $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
    if ($diff->s > 0) $partes[] = $diff->s . ' segundo' . ($diff->s > 1 ? 's' : '');

    return empty($partes) ? '0 segundos' : implode(', ', array_slice($partes, 0, 2));
}

function aMinusculas($str){
    return mb_strtolower($str,'UTF-8');
}
function diaActual(){
    return Carbon::now()->format('d');
}
function anioActual(){
    return Carbon::now()->year;
}

function formatearMs(int $ms): string
{
    $horas = floor($ms / 3600000);
    $ms %= 3600000;

    $minutos = floor($ms / 60000);
    $ms %= 60000;

    $segundos = floor($ms / 1000);
    $milisegundos = $ms % 1000;

    return sprintf(
        '%02d:%02d:%02d.%03d',
        $horas,
        $minutos,
        $segundos,
        $milisegundos
    );
}


