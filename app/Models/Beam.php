<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Beam extends Model
{
    use SoftDeletes;
    protected $table = 'tbl_beam';
    protected $guarded = [];
    protected $appends = ['no_beam', 'nomor', 'no_kikw', 'mesin', 'id_tenun'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getNomorAttribute()
    {
        $txtMesin = ($this->mesin == '') ? '' : ' | '. $this->mesin;
        return $this->relNomorKikw()->value('name') . $txtMesin . ' | ' . $this->tipe_beam;
    }

    public function getMesinAttribute()
    {
        return ($this->relMesinHistoryLatest()->first()) ? $this->relMesinHistoryLatest()->first()->relMesin()->value('name') : '';
    }

    public function getNoKikwAttribute()
    {
        return $this->relNomorKikw()->value('name');
    }

    public function getNoBeamAttribute()
    {
        return $this->relNomorBeam()->value('name');
    }

    public function getIdTenunAttribute()
    {
        return $this->relTenun()->value('id');
    }

    public function relWarping()
    {
        return $this->hasOne(WarpingDetail::class, 'id_beam', 'id');
    }

    public function relSizing()
    {
        return $this->hasOne(SizingDetail::class, 'id_beam', 'id');
    }

    public function relCucuk()
    {
        return $this->hasMany(Cucuk::class, 'id_beam', 'id');
    }

    public function relTyeing()
    {
        return $this->hasMany(Tyeing::class, 'id_beam', 'id');
    }

    public function relTenun()
    {
        return $this->hasOne(Tenun::class, 'id_beam', 'id');
    }

    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }

    public function relLogStokPenerimaan()
    {
        return $this->hasMany(LogStokPenerimaan::class, 'id_beam', 'id');
    }

    public function relWarpingDetail()
    {
        return $this->hasOne(WarpingDetail::class, 'id_beam', 'id');
    }

    public function relTenunDetail()
    {
        return $this->hasMany(TenunDetail::class, 'id_beam', 'id');
    }

    public function relNomorBeam()
    {
        return $this->belongsTo(NomorBeam::class, 'id_nomor_beam', 'id');
    }

    public function relNomorKikw()
    {
        return $this->belongsTo(NomorKikw::class, 'id_nomor_kikw', 'id');
    }

    public function relMesinHistory()
    {
        return $this->hasMany(MesinHistory::class, 'id_beam', 'id');
    }

    public function relMesinHistoryLatest()
    {
        return $this->hasOne(MesinHistory::class, 'id_beam', 'id')->latestOfMany();
    }

    public function relDistribusiPakanDetail()
    {
        return $this->hasMany(DistribusiPakanDetail::class, 'id_beam', 'id')->orderByDesc('id_distribusi_pakan');
    }

    public function relLogStokPenerimaanBL()
    {
        return $this->hasOne(LogStokPenerimaan::class, 'id_beam', 'id')->where('code', 'BL')->orderBy('id', 'asc');
    }
}
