<?php

namespace App\Http\Requests\Publico;

use App\Models\Candidato;
use App\Models\UserExterno;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistroCandidatoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $limpiar = fn ($v) => $v === null || trim((string) $v) === ''
            ? null
            : preg_replace('/\s+/', ' ', trim((string) $v));

        $this->merge([
            'primer_nombre' => $limpiar($this->input('primer_nombre')),
            'segundo_nombre' => $limpiar($this->input('segundo_nombre')),
            'tercer_nombre' => $limpiar($this->input('tercer_nombre')),
            'primer_apellido' => $limpiar($this->input('primer_apellido')),
            'segundo_apellido' => $limpiar($this->input('segundo_apellido')),
            'dpi' => preg_replace('/\s+/', '', (string) $this->input('dpi')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'telefono' => preg_replace('/[\s-]+/', '', (string) $this->input('telefono')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $nombre = "regex:/^[\\pL][\\pL .'-]*$/u";

        return [
            'primer_nombre' => ['required', 'string', 'min:2', 'max:45', $nombre],
            'segundo_nombre' => ['nullable', 'string', 'max:45', $nombre],
            'tercer_nombre' => ['nullable', 'string', 'max:45', $nombre],
            'primer_apellido' => ['required', 'string', 'min:2', 'max:45', $nombre],
            'segundo_apellido' => ['nullable', 'string', 'max:45', $nombre],
            'dpi' => [
                'required', 'digits:13',
                function ($attribute, $value, $fail) {
                    if (Candidato::where('dpi', $value)->exists()) {
                        $fail('Ya existe un candidato registrado con ese DPI.');
                    }
                },
            ],
            'email' => [
                'required', 'string', 'email:rfc,dns', 'max:100',
                function ($attribute, $value, $fail) {
                    if (UserExterno::where('email', $value)->exists()) {
                        $fail('Ya existe una cuenta con ese correo.');
                    }
                },
            ],
            'telefono' => ['required', 'regex:/^\+?[0-9]{8,11}$/'],
            'fecha_nacimiento' => [
                'required', 'date_format:Y-m-d',
                'after:1900-01-01',
                'before_or_equal:' . now()->subYears(18)->toDateString(),
            ],
            'password' => ['required', 'string', 'max:72', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'acepta_terminos' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'primer_nombre.regex' => 'El primer nombre contiene caracteres no permitidos.',
            'segundo_nombre.regex' => 'El segundo nombre contiene caracteres no permitidos.',
            'tercer_nombre.regex' => 'El tercer nombre contiene caracteres no permitidos.',
            'primer_apellido.regex' => 'El primer apellido contiene caracteres no permitidos.',
            'segundo_apellido.regex' => 'El segundo apellido contiene caracteres no permitidos.',
            'dpi.digits' => 'El DPI debe tener 13 dígitos.',
            'telefono.regex' => 'El teléfono debe tener entre 8 y 11 dígitos.',
            'fecha_nacimiento.before_or_equal' => 'Debes ser mayor de 18 años.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'acepta_terminos.accepted' => 'Debes aceptar los términos y condiciones.',
        ];
    }
}
