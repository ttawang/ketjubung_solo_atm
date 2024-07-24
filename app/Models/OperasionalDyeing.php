<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class OperasionalDyeing extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_operasional_dyeing';
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
        return $this->relOperasionalDyeingDetail->setAppends([])->count();
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function relOperasionalDyeingDetail()
    {
        return $this->hasMany(OperasionalDyeingDetail::class, 'id_operasional_dyeing', 'id');
    }
}
