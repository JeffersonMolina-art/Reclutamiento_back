<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as sanctumPersonalAccessToken;

class PersonalAccessToken extends sanctumPersonalAccessToken
{
    protected $table = 'reclutamiento.personal_access_tokens';
}
