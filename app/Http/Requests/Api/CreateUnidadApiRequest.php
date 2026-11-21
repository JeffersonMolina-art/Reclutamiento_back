<?php

namespace App\Http\Requests\Api;

use App\Models\Unidad;
use Illuminate\Foundation\Http\FormRequest;


class CreateUnidadApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

           return Unidad::$rules;

    }
}

