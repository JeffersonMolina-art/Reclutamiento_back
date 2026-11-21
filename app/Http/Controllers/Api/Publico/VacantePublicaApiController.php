<?php

namespace App\Http\Controllers\Api\Publico;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Publico\VacantePublicaResource;
use App\Models\Vacante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VacantePublicaApiController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $datos = $request->validate([
                'page' => 'nullable|integer|min:1',
                'buscar' => 'nullable|string|max:100',
            ]);

            $vacantes = Vacante::query()
                ->publicas()
                ->with(['puesto:id,nombre', 'sede:id,nombre', 'caracteristicas', 'requisitos'])
                ->when($datos['buscar'] ?? null, function ($query, $buscar) {
                    $buscar = addcslashes($buscar, '%_\\');
                    $query->where(function ($q) use ($buscar) {
                        $q->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('descripcion', 'like', "%{$buscar}%");
                    });
                })
                ->orderByDesc('fecha_publicacion')
                ->orderByDesc('id')
                ->paginate(10);

            return VacantePublicaResource::collection($vacantes)->response();
        });
    }
}
