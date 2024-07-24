<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use \Znck\Eloquent\Traits\BelongsToThrough;

class Tenun extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_tenun';
    protected $guarded = [];
    protected $appends = ['is_finish'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getIsFinishAttribute()
    {
        return $this->relBeam()->value('finish') == 1;
    }

    public function getCountDetailAttribute()
    {
        return $this->relTenunDetail->setAppends([])->count();
    }

    public function relTenunDetail()
    {
        return $this->hasMany(TenunDetail::class, 'id_tenun', 'id');
    }

    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }

    public function relSisaBeam()
    {
        $totalBeam = $this->jumlah_beam;
        $totalPotongan = $this->hasMany(TenunDetail::class, 'id_tenun', 'id')->where('code', 'BG')->groupBy('id_tenun')->sum('volume_1');
        return (int) $totalBeam - (int) $totalPotongan;
    }

    public function throughNomorKikw()
    {
        return $this->belongsToThrough(
            NomorKikw::class,
            Beam::class,
            null,
            '',
            [Beam::class => 'id_beam', NomorKikw::class => 'id_nomor_kikw']
        );
    }

    public function throughNomorBeam()
    {
        return $this->belongsToThrough(
            NomorBeam::class,
            Beam::class,
            null,
            '',
            [Beam::class => 'id_beam', NomorBeam::class => 'id_nomor_beam']
        );
    }

    public function relMesinTenun()
    {
        return $this->hasMany(MesinHistory::class, 'id_beam', 'id_beam');
    }

    public function relMesinHistoryLatest()
    {
        return $this->hasOne(MesinHistory::class, 'id_beam', 'id_beam')->latestOfMany();
    }
}
