<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Pakan extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_pakan';
    protected $guarded = [];

    protected $appends = ['tanggal_custom', 'count_detail'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getCountDetailAttribute()
    {
        return $this->relPakanDetail->setAppends([])->count();
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function relPakanDetail()
    {
        return $this->hasMany(PakanDetail::class, 'id_pakan', 'id');
    }
}
