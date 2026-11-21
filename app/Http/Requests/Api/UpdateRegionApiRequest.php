<?php

namespace App\Http\Requests\Api;

use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRegionApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

            return Region::$rules;

    }
}

