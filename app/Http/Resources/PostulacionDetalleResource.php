<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    class PostulacionDetalleResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            $vacante = $this->vacante;
            $candidato = $this->candidato;

            return [
                'id' => $this->id,
                'fecha_postulacion' => $this->fecha_postulacion,
                'estado' => $this->estado?->only(['id', 'nombre', 'codigo']),

                'vacante' => [
                    'id' => $vacante?->id,
                    'nombre' => $vacante?->nombre,
                    'salario_minimo' => $vacante?->salario_minimo,
                    'salario_maximo' => $vacante?->salario_maximo,
                    'puesto' => $vacante?->puesto?->only(['id', 'nombre']),
                    'sede' => $vacante?->sede?->only(['id', 'nombre']),
                    'requisitos' => $vacante?->requisitos
                        ->map(fn ($requisito) => $requisito->only(['id', 'nombre', 'es_obligatorio']))
                        ->values(),
                ],

                'candidato' => [
                    'id' => $candidato?->id,
                    'nombre_completo' => $candidato?->nombre_completo,
                    'primer_nombre' => $candidato?->primer_nombre,
                    'segundo_nombre' => $candidato?->segundo_nombre,
                    'tercer_nombre' => $candidato?->tercer_nombre,
                    'primer_apellido' => $candidato?->primer_apellido,
                    'segundo_apellido' => $candidato?->segundo_apellido,
                    'dpi' => $candidato?->dpi,
                    'correo_contacto' => $candidato?->correo_contacto,
                    'telefono_contacto' => $candidato?->telefono_contacto,
                    'fecha_nacimiento' => $candidato?->fecha_nacimiento,
                    'expectativa_salarial' => $candidato?->expectativa_salarial,
                    'conocimientos' => $candidato?->conocimientos
                        ->map(fn ($conocimiento) => $conocimiento->only(['id', 'descripcion', 'nivel']))
                        ->values(),
                    'experiencias' => $candidato?->experiencias
                        ->map(fn ($experiencia) => $experiencia->only(['id', 'empresa', 'puesto', 'fecha_inicio', 'fecha_fin']))
                        ->values(),
                    'formaciones' => $candidato?->formaciones
                        ->map(fn ($formacion) => $formacion->only(['id', 'nombre', 'titulo_carrera', 'fecha_inicio', 'fecha_fin']))
                        ->values(),
                ],

                'examenes' => $this->archivos(),
            ];
        }
    }
