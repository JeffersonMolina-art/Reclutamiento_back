<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserExternoEstado;

class UpdateUserExternoEstadoApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return UserExternoEstado::$rules;
    }
}

