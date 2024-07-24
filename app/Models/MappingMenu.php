<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingMenu extends Model
{
    protected $protected = 'mapping_menus';
    protected $guarded = [];

    function relMenu()
    {
        return $this->belongsTo(Menu::class, 'menus_id', 'id');
    }

    function relRole()
    {
        return $this->belongsTo(Role::class, 'roles_id', 'id');
    }
}
