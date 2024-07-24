<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Pekerja extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_pekerja';
    protected $guarded = [];
    protected $appends = ['nama_group'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getNamaGroupAttribute()
    {
        return $this->relGroup()->value('name');
    }

    public function relGroup()
    {
        return $this->belongsTo(Group::class, 'id_group', 'id');
    }

    public function relGroupDetail()
    {
        return $this->hasOne(GroupDetail::class, 'id_pekerja', 'id');
    }
}
