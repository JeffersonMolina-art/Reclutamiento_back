<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateVacanteApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado_id' => ['required', 'integer', 'exists:reclutamiento.vacantes_estados,id'],
            'puesto_id' => ['required', 'integer', 'exists:reclutamiento.puestos,id'],
            'sede_id' => ['required', 'integer', 'exists:reclutamiento.sede,id'],
            'nombre' => ['required', 'string', 'max:45'],
            'descripcion' => ['required', 'string'],
            'cantidad_plazas' => ['required', 'integer', 'min:1'],
            'fecha_publicacion' => ['nullable', 'date'],
            'user_encargado' => ['nullable', 'integer', 'exists:reclutamiento.users,id'],
            'modalidad_trabajo' => ['required', Rule::in(['presencial', 'remoto', 'hibrido'])],
            'salario_minimo' => ['required', 'numeric', 'min:0'],
            'salario_maximo' => ['required', 'numeric', 'gte:salario_minimo'],

            'requisitos' => ['nullable', 'array'],
            'requisitos.*.nombre' => ['required', 'string', 'max:255'],
            'requisitos.*.es_obligatorio' => ['required', 'boolean'],

            'caracteristicas' => ['nullable', 'array'],
            'caracteristicas.*.nombre' => ['required', 'string', 'max:255'],
            'caracteristicas.*.valor' => ['nullable', 'string', 'max:45'],
        ];
    }
}
