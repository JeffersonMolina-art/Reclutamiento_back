<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\EntrevistaEstado;

class UpdateEntrevistaEstadoApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return EntrevistaEstado::$rules;
    }
}

