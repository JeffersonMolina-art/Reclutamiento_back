<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{

    protected $table = 'reclutamiento.permissions';

    protected $fillable = ['name','subject', 'guard_name'];
    protected $appends = ['name_y_subject'];

    public function getNameYSubjectAttribute(): string
    {

        return $this->name . ' - ' . $this->subject;

    }

    public function getRolesSeparadosComaAttribute(): string
    {
        return $this->roles->pluck('name')->implode(', ');
    }

}
