<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\SIGUA\Asociado;
use App\Models\SIGUA\ListaInterna;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


/**
 * Class ListaInternaApiController
 */
class ListaInternaApiController extends AppbaseController implements HasMiddleware
{


    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
        new Middleware('abilities:ver lista-internas', only: ['verificarCodigo']),
        ];
    }

    public function verificarCodigo(Asociado $asociado)
    {
        if ($asociado) {
            $listaInterna = ListaInterna::where([
                ['estado_id', 1],
                ['codigo', 1]
            ])
                ->whereNotNull('numero_identificacion')
                ->where(function ($query) use ($asociado) {
                    $query
                        ->where('numero_identificacion', $asociado->dpi)
                        ->orWhere('numero_identificacion', $asociado->cedula);
                })
                ->orderBy('created_at', 'desc') // Ordenar por el más reciente
                ->first(); // Obtener solo el más reciente

            if ($listaInterna) {
                return $this->sendResponse($listaInterna, 'Última lista interna encontrada.');
            }
        }

        return $this->sendError('No se encontraron listas internas');
    }




}
