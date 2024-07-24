<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = "Benang TR 40";
        $input[0]['nomor'] = "TR 40";
        $input[0]['kode'] = "B001";
        $input[0]['alias'] = "Benang TR 40";
        $input[0]['id_tipe'] = 1;
        $input[0]['created_at'] = now();
        $input[0]['updated_at'] = now();

        $input[1]['name'] = "Benang RY 30";
        $input[1]['nomor'] = "RY 30";
        $input[1]['kode'] = "B002";
        $input[1]['alias'] = "Benang RY 30";
        $input[1]['id_tipe'] = 1;
        $input[1]['created_at'] = now();
        $input[1]['updated_at'] = now();

        $input[2]['name'] = "Benang TR 30s";
        $input[2]['nomor'] = "TR 30s";
        $input[2]['kode'] = "B003";
        $input[2]['alias'] = "Benang TR 30s";
        $input[2]['id_tipe'] = 1;
        $input[2]['created_at'] = now();
        $input[2]['updated_at'] = now();

        $input[3]['name'] = "Benang TR 50/2";
        $input[3]['nomor'] = "TR 50/2";
        $input[3]['kode'] = "B004";
        $input[3]['alias'] = "Benang TR 50/2";
        $input[3]['id_tipe'] = 1;
        $input[3]['created_at'] = now();
        $input[3]['updated_at'] = now();

        $input[4]['name'] = "Benang RT 80/2";
        $input[4]['nomor'] = "RT 80/2";
        $input[4]['kode'] = "B005";
        $input[4]['alias'] = "Benang RT 80/2";
        $input[4]['id_tipe'] = 1;
        $input[4]['created_at'] = now();
        $input[4]['updated_at'] = now();

        $input[5]['name'] = "Chemical Merah";
        $input[5]['nomor'] = "M001";
        $input[5]['kode'] = "M001";
        $input[5]['alias'] = "Chemical Merah M001";
        $input[5]['id_tipe'] = 2;
        $input[5]['created_at'] = now();
        $input[5]['updated_at'] = now();

        $input[6]['name'] = "Chemical Biru Biru";
        $input[6]['nomor'] = "B001";
        $input[6]['kode'] = "B001";
        $input[6]['alias'] = "Chemical Biru B001";
        $input[6]['id_tipe'] = 2;
        $input[6]['created_at'] = now();
        $input[6]['updated_at'] = now();

        $input[7]['name'] = "Beam Lusi TR 30s";
        $input[7]['nomor'] = "TR 30s";
        $input[7]['kode'] = "BL001";
        $input[7]['alias'] = "Beam Lusi TR 30s";
        $input[7]['id_tipe'] = 3;
        $input[7]['created_at'] = now();
        $input[7]['updated_at'] = now();

        $input[8]['name'] = "Beam Songket TR 50/2";
        $input[8]['nomor'] = "TR 50/2";
        $input[8]['kode'] = "BS001";
        $input[8]['alias'] = "Beam Songket TR 50/2";
        $input[8]['id_tipe'] = 4;
        $input[8]['created_at'] = now();
        $input[8]['updated_at'] = now();

        $input[9]['name'] = "Pakan TR 40";
        $input[9]['nomor'] = "TR 40";
        $input[9]['kode'] = "P001";
        $input[9]['alias'] = "Pakan TR 40";
        $input[9]['id_tipe'] = 5;
        $input[9]['created_at'] = now();
        $input[9]['updated_at'] = now();

        $input[10]['name'] = "Leno TR 50/2";
        $input[10]['nomor'] = "TR 50/2";
        $input[10]['kode'] = "BO001";
        $input[10]['alias'] = "Leno TR 50/2";
        $input[10]['id_tipe'] = 6;
        $input[10]['created_at'] = now();
        $input[10]['updated_at'] = now();

        $input[11]['name'] = "Sarung TR 50/2";
        $input[11]['nomor'] = "TR 50/2";
        $input[11]['kode'] = "S001";
        $input[11]['alias'] = "Sarung TR 50/2";
        $input[11]['id_tipe'] = 7;
        $input[11]['created_at'] = now();
        $input[11]['updated_at'] = now();

        $input[12]['name'] = "Chemical IT";
        $input[12]['nomor'] = "Chemical IT";
        $input[12]['kode'] = "Chemical IT";
        $input[12]['alias'] = "Chemical IT";
        $input[12]['id_tipe'] = 2;
        $input[12]['created_at'] = now();
        $input[12]['updated_at'] = now();

        $input[13]['name'] = "Dianix Yellow CC";
        $input[13]['nomor'] = "Dianix Yellow CC";
        $input[13]['kode'] = "Dianix Yellow CC";
        $input[13]['alias'] = "Dianix Yellow CC";
        $input[13]['id_tipe'] = 2;
        $input[13]['created_at'] = now();
        $input[13]['updated_at'] = now();

        $input[14]['name'] = "Atlacron Rubine RDFLN 200%";
        $input[14]['nomor'] = "Atlacron Rubine RDFLN 200%";
        $input[14]['kode'] = "Atlacron Rubine RDFLN 200%";
        $input[14]['alias'] = "Atlacron Rubine RDFLN 200%";
        $input[14]['id_tipe'] = 2;
        $input[14]['created_at'] = now();
        $input[14]['updated_at'] = now();

        $input[15]['name'] = "Palanil Black SERN 300%";
        $input[15]['nomor'] = "Palanil Black SERN 300%";
        $input[15]['kode'] = "Palanil Black SERN 300%";
        $input[15]['alias'] = "Palanil Black SERN 300%";
        $input[15]['id_tipe'] = 2;
        $input[15]['created_at'] = now();
        $input[15]['updated_at'] = now();
        
        $input[16]['name'] = "Remasol Gold Yellow RGB Conc";
        $input[16]['nomor'] = "Remasol Gold Yellow RGB Conc";
        $input[16]['kode'] = "Remasol Gold Yellow RGB Conc";
        $input[16]['alias'] = "Remasol Gold Yellow RGB Conc";
        $input[16]['id_tipe'] = 2;
        $input[16]['created_at'] = now();
        $input[16]['updated_at'] = now();

        $input[17]['name'] = "Remasol Brill Orange 3R SP";
        $input[17]['nomor'] = "Remasol Brill Orange 3R SP";
        $input[17]['kode'] = "Remasol Brill Orange 3R SP";
        $input[17]['alias'] = "Remasol Brill Orange 3R SP";
        $input[17]['id_tipe'] = 2;
        $input[17]['created_at'] = now();
        $input[17]['updated_at'] = now();

        $input[18]['name'] = "Remasol Deep Black RGB";
        $input[18]['nomor'] = "Remasol Deep Black RGB";
        $input[18]['kode'] = "Remasol Deep Black RGB";
        $input[18]['alias'] = "Remasol Deep Black RGB";
        $input[18]['id_tipe'] = 2;
        $input[18]['created_at'] = now();
        $input[18]['updated_at'] = now();

        Barang::insert($input);
    }
}
