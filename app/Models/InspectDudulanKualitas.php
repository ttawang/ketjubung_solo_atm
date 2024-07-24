<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class InspectDudulanKualitas extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_inspect_dudulan_kualitas';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }
    public function relInspectDudulanDetail()
    {
        return $this->belongsTo(InspectDudulanDetail::class, 'id_inspect_dudulan_detail', 'id');
    }
    public function relKualitas()
    {
        return $this->belongsTo(MappingKualitas::class, 'id_kualitas', 'id');
    }
}
