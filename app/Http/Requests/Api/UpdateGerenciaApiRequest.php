<?php

namespace App\Http\Requests\Api;

use App\Models\Gerencia;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGerenciaApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

            return Gerencia::$rules;

    }
}

