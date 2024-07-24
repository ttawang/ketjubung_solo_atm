<?php

namespace Database\Seeders;

use App\Models\TipePengiriman;
use Illuminate\Database\Seeder;

class TipePengirimanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = "Bukti Penyerahan Benang Baku";
        $input[0]['title'] = "Bukti Penyerahan Benang Baku (BPBB)";
        $input[0]['initial'] = "BPBB";
        $input[0]['is_aktif'] = "YA";
        $input[0]['id_gudang_asal'] = 1;
        $input[0]['id_gudang_tujuan'] = 2;
        $input[0]['roles_id'] = 7;
        $input[0]['created_by'] = 1;
        $input[0]['created_at'] = now();
        $input[0]['updated_at'] = now();

        $input[1]['name'] = "Bukti Penyerahan Hasil Dyeing";
        $input[1]['title'] = "Bukti Penyerahan Hasil Dyeing";
        $input[1]['initial'] = "BPHD";
        $input[1]['is_aktif'] = "YA";
        $input[1]['id_gudang_asal'] = 2;
        $input[1]['id_gudang_tujuan'] = 1;
        $input[1]['roles_id'] = 2;
        $input[1]['created_by'] = 1;
        $input[1]['created_at'] = now();
        $input[1]['updated_at'] = now();

        $input[2]['name'] = "Bukti Penyerahan Benang Warna";
        $input[2]['title'] = "Bukti Penyerahan Benang Warna (BPBW)";
        $input[2]['inisial'] = "BPBW";
        $input[2]['is_aktif'] = "YA";
        $input[2]['id_gudang_asal'] = 1;
        $input[2]['id_gudang_tujuan'] = 3;
        $input[2]['roles_id'] = 7;
        $input[2]['created_by'] = 1;
        $input[2]['created_at'] = now();
        $input[2]['updated_at'] = now();

        $input[3]['name'] = "Bukti Penyerahan Benang Warna Sisa (Retur)";
        $input[3]['title'] = "Bukti Penyerahan Benang Warna Sisa (BPBWR)";
        $input[3]['inisial'] = "BPBWR";
        $input[3]['is_aktif'] = "YA";
        $input[3]['id_gudang_asal'] = 3;
        $input[3]['id_gudang_tujuan'] = 1;
        $input[3]['roles_id'] = 3;
        $input[3]['created_by'] = 1;
        $input[3]['created_at'] = now();
        $input[3]['updated_at'] = now();

        $input[4]['name'] = "Bukti Penyerahan Beam Lusi";
        $input[4]['title'] = "Bukti Penyerahan Beam Lusi Ke Mesin Tenun (BPBL)";
        $input[4]['inisial'] = "BPBL";
        $input[4]['is_aktif'] = "YA";
        $input[4]['id_gudang_asal'] = 3;
        $input[4]['id_gudang_tujuan'] = 4;
        $input[4]['roles_id'] = 3;
        $input[4]['created_by'] = 1;
        $input[4]['created_at'] = now();
        $input[4]['updated_at'] = now();

        $input[5]['name'] = "Bukti Penyerahan Beam Songket";
        $input[5]['title'] = "Bukti Penyerahan Beam Songket Ke Mesin Tenun (BPBS)";
        $input[5]['inisial'] = "BPBS";
        $input[5]['is_aktif'] = "YA";
        $input[5]['id_gudang_asal'] = 3;
        $input[5]['id_gudang_tujuan'] = 4;
        $input[5]['roles_id'] = 1;
        $input[5]['created_by'] = 1;
        $input[5]['created_at'] = now();
        $input[5]['updated_at'] = now();

        $input[6]['name'] = "Bukti Penyerahan Hasil Tenun";
        $input[6]['title'] = "Bukti Penyerahan Hasil Tenun Ke Inspekting (BPHT)";
        $input[6]['inisial'] = "BPHT";
        $input[6]['is_aktif'] = "YA";
        $input[6]['id_gudang_asal'] = 5;
        $input[6]['id_gudang_tujuan'] = 5;
        $input[6]['roles_id'] = 3;
        $input[6]['created_by'] = 1;
        $input[6]['created_at'] = now();
        $input[6]['updated_at'] = now();

        $input[7]['name'] = "Bukti Penyerahan Sarung Grey";
        $input[7]['title'] = "Bukti Penyerahan Sarung Grey Ke Finishing (BPSG)";
        $input[7]['inisial'] = "BPSG";
        $input[7]['is_aktif'] = "YA";
        $input[7]['id_gudang_asal'] = 5;
        $input[7]['id_gudang_tujuan'] = 6;
        $input[7]['roles_id'] = 5;
        $input[7]['created_by'] = 1;
        $input[7]['created_at'] = now();
        $input[7]['updated_at'] = now();

        $input[8]['name'] = "Bukti Penyerahan Bahan Pewarna";
        $input[8]['title'] = "Bukti Penyerahan Bahan Pewarna (BPBP)";
        $input[8]['inisial'] = "BPBP";
        $input[8]['is_aktif'] = "TIDAK";
        $input[8]['id_gudang_asal'] = 1;
        $input[8]['id_gudang_tujuan'] = 2;
        $input[8]['roles_id'] = 2;
        $input[8]['created_by'] = 1;
        $input[8]['created_at'] = now();
        $input[8]['updated_at'] = now();

        $input[9]['name'] = "Bukti Pengiriman Barang Antar Gudang";
        $input[9]['title'] = "Bukti Pengiriman Barang Antar Gudang (BPBAG)";
        $input[9]['inisial'] = "BPBAG";
        $input[9]['is_aktif'] = "TIDAK";
        $input[9]['id_gudang_asal'] = 1;
        $input[9]['id_gudang_tujuan'] = 1;
        $input[9]['roles_id'] = 4;
        $input[9]['created_by'] = 1;
        $input[9]['created_at'] = now();
        $input[9]['updated_at'] = now();

        $input[10]['name'] = "Bukti Penyerahan Retur Beam Lusi Problem";
        $input[10]['title'] = "Bukti Penyerahan Retur Beam Lusi Problem ke Warping";
        $input[10]['inisial'] = "BPBTR";
        $input[10]['is_aktif'] = "YA";
        $input[10]['id_gudang_asal'] = 4;
        $input[10]['id_gudang_tujuan'] = 3;
        $input[10]['roles_id'] = 4;
        $input[10]['created_by'] = 1;
        $input[10]['created_at'] = now();
        $input[10]['updated_at'] = now();

        $input[11]['name'] = "Bukti Penyerahan Retur Sisa Songket";
        $input[11]['title'] = "Bukti Penyerahan Retur Sisa Songket";
        $input[11]['inisial'] = "BPBSS";
        $input[11]['is_aktif'] = "YA";
        $input[11]['id_gudang_asal'] = 4;
        $input[11]['id_gudang_tujuan'] = 3;
        $input[11]['roles_id'] = 4;
        $input[11]['created_by'] = 1;
        $input[11]['created_at'] = now();
        $input[11]['updated_at'] = now();

        $input[12]['name'] = "Bukti Penyerahan Retur Benang Warna Sisa Pakan Shuttle";
        $input[12]['title'] = "Bukti Penyerahan Retur Benang Warna Sisa Pakan (Shuttle)";
        $input[12]['inisial'] = "BPBWSPS";
        $input[12]['is_aktif'] = "YA";
        $input[12]['id_gudang_asal'] = 4;
        $input[12]['id_gudang_tujuan'] = 7;
        $input[12]['roles_id'] = 4;
        $input[12]['created_by'] = 1;
        $input[12]['created_at'] = now();
        $input[12]['updated_at'] = now();

        $input[13]['name'] = "Bukti Penyerahan Retur Benang Warna Sisa Pakan Rappier";
        $input[13]['title'] = "Bukti Penyerahan Retur Benang Warna Sisa Pakan (Rappier)";
        $input[13]['inisial'] = "BPBWSPR";
        $input[13]['is_aktif'] = "YA";
        $input[13]['id_gudang_asal'] = 4;
        $input[13]['id_gudang_tujuan'] = 7;
        $input[13]['roles_id'] = 4;
        $input[13]['created_by'] = 1;
        $input[13]['created_at'] = now();
        $input[13]['updated_at'] = now();

        $input[14]['name'] = "Bukti Penyerahan Beam Lusi Nomor Baru";
        $input[14]['title'] = "Bukti Penyerahan Beam Lusi Nomor Baru";
        $input[14]['inisial'] = "BPBLNB";
        $input[14]['is_aktif'] = "YA";
        $input[14]['id_gudang_asal'] = 3;
        $input[14]['id_gudang_tujuan'] = 4;
        $input[14]['roles_id'] = 3;
        $input[14]['created_by'] = 1;
        $input[14]['created_at'] = now();
        $input[14]['updated_at'] = now();

        $input[15]['name'] = "Bukti Penyerahan Beam Songket Nomor Baru";
        $input[15]['title'] = "Bukti Penyerahan Beam Songket Nomor Baru";
        $input[15]['inisial'] = "BPBSNB";
        $input[15]['is_aktif'] = "YA";
        $input[15]['id_gudang_asal'] = 3;
        $input[15]['id_gudang_tujuan'] = 4;
        $input[15]['roles_id'] = 3;
        $input[15]['created_by'] = 1;
        $input[15]['created_at'] = now();
        $input[15]['updated_at'] = now();

        $input[16]['name'] = "Bukti Penyerahan Benang Warna Sisa Tidak Terpakai (Retur)";
        $input[16]['title'] = "Bukti Penyerahan Benang Warna Sisa Tidak Terpakai (BPBWRS)";
        $input[16]['inisial'] = "BPBWRS";
        $input[16]['is_aktif'] = "YA";
        $input[16]['id_gudang_asal'] = 3;
        $input[16]['id_gudang_tujuan'] = 1;
        $input[16]['roles_id'] = 3;
        $input[16]['created_by'] = 1;
        $input[16]['created_at'] = now();
        $input[16]['updated_at'] = now();

        TipePengiriman::insert($input);
    }
}
