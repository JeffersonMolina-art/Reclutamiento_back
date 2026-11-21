<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Configuracion;

class UpdateConfiguracionApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

            return Configuracion::$rules;

    }
}

