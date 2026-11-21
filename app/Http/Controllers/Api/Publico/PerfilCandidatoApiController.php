<?php

namespace App\Http\Controllers\Api\Publico;

use App\Http\Controllers\AppBaseController;
use App\Models\Candidato;
use App\Models\UserExterno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PerfilCandidatoApiController extends AppBaseController
{
    private const MIMES_CV = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    public function show(Request $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $candidato = $this->candidato($request);

            return response()->json(['data' => [
                'datos' => $this->datos($candidato),
                'conocimientos' => $candidato->conocimientos()->orderBy('id')->get(['id', 'descripcion', 'nivel']),
                'experiencias' => $candidato->experiencias()->orderByDesc('fecha_inicio')->get(['id', 'empresa', 'puesto', 'fecha_inicio', 'fecha_fin']),
                'formaciones' => $candidato->formaciones()->orderByDesc('fecha_inicio')->get(['id', 'nombre', 'titulo_carrera', 'fecha_inicio', 'fecha_fin']),
                'cv' => $this->estadoCv($candidato),
            ]]);
        });
    }
    public function update(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'primer_nombre' => 'required|string|max:100',
            'segundo_nombre' => 'nullable|string|max:100',
            'tercer_nombre' => 'nullable|string|max:100',
            'primer_apellido' => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'correo_contacto' => 'required|email:rfc|max:191',
            'telefono_contacto' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{8,20}$/'],
            'fecha_nacimiento' => 'required|date|before:-16 years|after:1900-01-01',
            'expectativa_salarial' => 'required|numeric|min:0|max:99999999',
        ]);

        return $this->executeTransaction(function () use ($request, $datos) {
            $candidato = $this->candidato($request);
            $candidato->update($datos);

            return response()->json(['message' => 'Perfil actualizado.', 'data' => $this->datos($candidato->refresh())]);
        });
    }
    public function cv(Request $request): JsonResponse
    {
        return $this->executeOperation(fn () => response()->json(['data' => $this->estadoCv($this->candidato($request))]));
    }
    public function subirCv(Request $request): JsonResponse
    {
        $request->validate([
            'cv' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx', 'mimetypes:'.implode(',', self::MIMES_CV)],
        ], [
            'cv.mimes' => 'El CV debe ser un archivo PDF, DOC o DOCX.',
            'cv.mimetypes' => 'El CV debe ser un archivo PDF, DOC o DOCX.',
            'cv.max' => 'El CV no debe pesar más de 5 MB.',
        ]);

        return $this->executeTransaction(function () use ($request) {
            $candidato = $this->candidato($request);

            $candidato->addMediaFromRequest('cv')
                ->usingFileName(bin2hex(random_bytes(16)).'.'.$request->file('cv')->getClientOriginalExtension())
                ->usingName('CV')
                ->toMediaCollection(Candidato::COLECCION_CV);

            return response()->json(['message' => 'CV cargado correctamente.', 'data' => $this->estadoCv($candidato->refresh())], 201);
        });
    }
    public function descargarCv(Request $request): StreamedResponse|JsonResponse
    {
        $cv = $this->candidato($request)->cvActual();

        if (! $cv) {
            return response()->json(['message' => 'No tienes un CV cargado.'], 404);
        }

        return $cv->toResponse($request);
    }
    public function eliminarCv(Request $request): JsonResponse
    {
        return $this->executeOperation(function () use ($request) {
            $this->candidato($request)->clearMediaCollection(Candidato::COLECCION_CV);

            return response()->json(['message' => 'CV eliminado.']);
        });
    }
    private function estadoCv(Candidato $candidato): array
    {
        /** @var Media|null $cv */
        $cv = $candidato->cvActual();

        if (! $cv) {
            return [
                'tiene_cv' => false,
                'vigente' => false,
                'requiere_actualizar' => true,
                'motivo' => 'Aún no has subido tu CV.',
            ];
        }

        $vence = $cv->created_at->copy()->addYears(Candidato::CV_VIGENCIA_ANIOS);
        $vigente = $vence->isFuture();

        return [
            'tiene_cv' => true,
            'vigente' => $vigente,
            'requiere_actualizar' => ! $vigente,
            'motivo' => $vigente ? null : 'Tu CV tiene más de un año, súbelo de nuevo para poder postularte.',
            'subido_el' => $cv->created_at->toIso8601String(),
            'vence_el' => $vence->toDateString(),
            'extension' => pathinfo($cv->file_name, PATHINFO_EXTENSION),
            'tamano' => $cv->size,
        ];
    }

    private function datos(Candidato $candidato): array
    {
        return $candidato->only([
            'dpi', 'primer_nombre', 'segundo_nombre', 'tercer_nombre', 'primer_apellido', 'segundo_apellido',
            'correo_contacto', 'telefono_contacto', 'expectativa_salarial',
        ]) + ['fecha_nacimiento' => $candidato->fecha_nacimiento?->toDateString()];
    }
    private function candidato(Request $request): Candidato
    {
        /** @var UserExterno $usuario */
        $usuario = $request->user();

        return $usuario->candidato()->firstOrFail();
    }
}
