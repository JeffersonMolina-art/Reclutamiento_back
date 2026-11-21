<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserFavorito;



class CreateUserFavoritoApiRequest extends FormRequest
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

