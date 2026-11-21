<?php

namespace App\Http\Requests\Api;

use App\Models\Puesto;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePuestoApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

            return Puesto::$rules;

    }
}

