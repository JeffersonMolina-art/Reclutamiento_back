<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserFavorito;

class UpdateUserFavoritoApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

            return UserFavorito::$rules;

    }
}

