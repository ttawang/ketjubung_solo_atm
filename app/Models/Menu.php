<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $guarded = [];
    protected $appends = ['have_child'];

    function relMapping(){
        return $this->hasMany(MappingMenu::class, 'menus_id', 'id');
    }

    function relParent(){
        return $this->belongsTo(Menu::class, 'parent_id', 'id')->withDefault();
    }

    function getHaveChildAttribute(){
        return $this->hasMany(Menu::class, 'parent_id', 'id')->where('parent_id', $this->id)->count() > 0;
    }

    function relSelectParent(){
        return $this->whereNull('link');
    }
}
