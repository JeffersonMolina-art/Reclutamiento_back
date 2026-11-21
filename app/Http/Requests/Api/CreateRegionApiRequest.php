<?php

namespace App\Http\Requests\Api;

use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;


class CreateRegionApiRequest extends FormRequest
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

