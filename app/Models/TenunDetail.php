<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Znck\Eloquent\Traits\BelongsToThrough;

class TenunDetail extends Model
{
    use SoftDeletes, BelongsToThrough;
    protected $table = 'tbl_tenun_detail';
    protected $guarded = [];
    protected $appends = ['tanggal_custom', 'nama_barang', 'nama_satuan_1', 'nama_satuan_2', 'proses', 'songket'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            $table->created_by = Auth::id();
        });
    }

    public function getProsesAttribute()
    {
        $idWarna      = $this->id_warna;
        $idMotif      = $this->id_motif;
        $idBeam       = $this->id_beam;
        $idBarang     = $this->id_barang;
        $countProsesTurun = DB::table('tbl_tenun_detail')
            ->where('id_tenun', $this->id_tenun)
            ->where('code', $this->code . 'T')
            ->where(function ($query) use ($idBarang, $idWarna, $idMotif, $idBeam) {
                $query
                    ->where('id_barang', $idBarang)
                    ->when($idWarna, function ($query) use ($idWarna) {
                        return $query->where('id_warna', $idWarna);
                    })->when($idMotif, function ($query) use ($idMotif) {
                        return $query->where('id_motif', $idMotif);
                    })->when($idBeam, function ($query) use ($idBeam) {
                        return $query->where('id_beam', $idBeam);
                    });
            })
            ->whereNull('deleted_at')->count();
        return $countProsesTurun > 0 ? 'Diturunkan' : 'Diproses';
    }

    public function getSongketAttribute()
    {
        if ($this->id_songket_detail != null) {
            return $this->relSongketDetail->nama_barang;
        }
        return '';
    }

    public function getTanggalCustomAttribute()
    {
        return Date::format($this->tanggal, 98);
    }

    public function getNamaSatuan1Attribute()
    {
        return $this->relSatuan1()->value('name');
    }

    public function getNamaSatuan2Attribute()
    {
        return $this->relSatuan2()->value('name');
    }

    public function getNamaBarangAttribute()
    {
        $txtWarna = $txtPakan = $txtMotif = $txtNoKikw = '';
        if ($this->id_warna != null) $txtWarna = ' | ' . $this->relWarna()->value('alias');
        if ($this->id_motif != null) $txtMotif = ' | ' . $this->relMotif()->value('alias');
        if ($this->code == 'DPS' || $this->code == 'DPST') $txtPakan = ' | ' . 'Palet';
        if ($this->code == 'DPR' || $this->code == 'DPRT') $txtPakan = ' | ' . 'Cone';
        if ($this->code == 'BBTS' && $this->id_beam != null) $txtNoKikw = $this->throughNomorKikw()->value('name') . ' | ';
        return $txtNoKikw . '' . $this->relBarang()->value('name') . '' . $txtPakan . '' . $txtWarna . '' . $txtMotif;
    }

    public function relBarang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id');
    }

    public function relSatuan1()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_1', 'id');
    }

    public function relSatuan2()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_2', 'id');
    }

    public function relWarna()
    {
        return $this->belongsTo(Warna::class, 'id_warna', 'id');
    }

    public function relMotif()
    {
        return $this->belongsTo(Motif::class, 'id_motif', 'id');
    }

    public function relGudang()
    {
        return $this->belongsTo(Gudang::class, 'id_gudang', 'id');
    }

    public function relGrade()
    {
        return $this->belongsTo(Kualitas::class, 'id_grade', 'id');
    }

    public function relKualitas()
    {
        return $this->belongsTo(MappingKualitas::class, 'id_kualitas', 'id');
    }

    public function relGroup()
    {
        return $this->belongsTo(Group::class, 'id_group', 'id');
    }

    public function relPekerja()
    {
        return $this->belongsTo(Pekerja::class, 'id_pekerja', 'id');
    }

    public function relBeam()
    {
        return $this->belongsTo(Beam::class, 'id_beam', 'id');
    }

    public function relMesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin', 'id');
    }

    public function relTenun()
    {
        return $this->belongsTo(Tenun::class, 'id_tenun', 'id');
    }

    public function relLusiTurun()
    {
        return $this->hasMany(TenunDetail::class, 'id_tenun', 'id_tenun')->where('code', 'BBTLT');
    }

    public function relSongketTurun()
    {
        return $this->hasMany(TenunDetail::class, 'id_tenun', 'id_tenun')->where('code', 'BBTST');
    }

    public function relSongketPotong()
    {
        return $this->hasMany(TenunDetail::class, 'id_songket_detail', 'id')->where('code', 'BG');
    }

    public function relSongketDetail()
    {
        return $this->belongsTo(TenunDetail::class, 'id_songket_detail', 'id');
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
}
