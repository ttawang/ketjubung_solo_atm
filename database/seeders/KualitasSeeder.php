<?php

namespace Database\Seeders;

use App\Models\MappingKualitas;
use Illuminate\Database\Seeder;

class KualitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['id_kualitas'] = 2;
        $input[0]['kode'] = 'Jl';
        $input[0]['name'] = 'Jlumbat';
        $input[0]['created_by'] = 1;
        $input[0]['created_at'] = date('Y-m-d H:i:s');
        $input[0]['updated_at'] = date('Y-m-d H:i:s');

        $input[1]['id_kualitas'] = 2;
        $input[1]['kode'] = 'FR';
        $input[1]['name'] = 'Float Ringan';
        $input[1]['created_by'] = 1;
        $input[1]['created_at'] = date('Y-m-d H:i:s');
        $input[1]['updated_at'] = date('Y-m-d H:i:s');

        $input[2]['id_kualitas'] = 2;
        $input[2]['kode'] = 'Ngd';
        $input[2]['name'] = 'Nganduk';
        $input[2]['created_by'] = 1;
        $input[2]['created_at'] = date('Y-m-d H:i:s');
        $input[2]['updated_at'] = date('Y-m-d H:i:s');

        $input[3]['id_kualitas'] = 2;
        $input[3]['kode'] = 'SK';
        $input[3]['name'] = 'Sobek Kecil';
        $input[3]['created_by'] = 1;
        $input[3]['created_at'] = date('Y-m-d H:i:s');
        $input[3]['updated_at'] = date('Y-m-d H:i:s');

        $input[4]['id_kualitas'] = 2;
        $input[4]['kode'] = 'TI';
        $input[4]['name'] = 'Take In';
        $input[4]['created_by'] = 1;
        $input[4]['created_at'] = date('Y-m-d H:i:s');
        $input[4]['updated_at'] = date('Y-m-d H:i:s');

        $input[5]['id_kualitas'] = 2;
        $input[5]['kode'] = 'BN';
        $input[5]['name'] = 'Benang Masuk';
        $input[5]['created_by'] = 1;
        $input[5]['created_at'] = date('Y-m-d H:i:s');
        $input[5]['updated_at'] = date('Y-m-d H:i:s');

        $input[6]['id_kualitas'] = 3;
        $input[6]['kode'] = 'SB';
        $input[6]['name'] = 'Sobek Besar';
        $input[6]['created_by'] = 1;
        $input[6]['created_at'] = date('Y-m-d H:i:s');
        $input[6]['updated_at'] = date('Y-m-d H:i:s');

        $input[7]['id_kualitas'] = 3;
        $input[7]['kode'] = 'SS';
        $input[7]['name'] = 'Sobek Songket';
        $input[7]['created_by'] = 1;
        $input[7]['created_at'] = date('Y-m-d H:i:s');
        $input[7]['updated_at'] = date('Y-m-d H:i:s');

        $input[8]['id_kualitas'] = 3;
        $input[8]['kode'] = 'FB';
        $input[8]['name'] = 'Floating Besar';
        $input[8]['created_by'] = 1;
        $input[8]['created_at'] = date('Y-m-d H:i:s');
        $input[8]['updated_at'] = date('Y-m-d H:i:s');

        $input[9]['id_kualitas'] = 3;
        $input[9]['kode'] = 'TTA';
        $input[9]['name'] = 'Tepi Tak Teranyan';
        $input[9]['created_by'] = 1;
        $input[9]['created_at'] = date('Y-m-d H:i:s');
        $input[9]['updated_at'] = date('Y-m-d H:i:s');

        $input[10]['id_kualitas'] = 3;
        $input[10]['kode'] = 'KNg';
        $input[10]['name'] = 'Kuku Ngrigis';
        $input[10]['created_by'] = 1;
        $input[10]['created_at'] = date('Y-m-d H:i:s');
        $input[10]['updated_at'] = date('Y-m-d H:i:s');

        $input[11]['id_kualitas'] = 3;
        $input[11]['kode'] = 'BA';
        $input[11]['name'] = 'Bekas Ambrol';
        $input[11]['created_by'] = 1;
        $input[11]['created_at'] = date('Y-m-d H:i:s');
        $input[11]['updated_at'] = date('Y-m-d H:i:s');

        $input[12]['id_kualitas'] = 3;
        $input[12]['kode'] = 'BNd';
        $input[12]['name'] = 'Bekas Ndedel';
        $input[12]['created_by'] = 1;
        $input[12]['created_at'] = date('Y-m-d H:i:s');
        $input[12]['updated_at'] = date('Y-m-d H:i:s');

        $input[13]['id_kualitas'] = 3;
        $input[13]['kode'] = 'PjE';
        $input[13]['name'] = 'Panjang Error';
        $input[13]['created_by'] = 1;
        $input[13]['created_at'] = date('Y-m-d H:i:s');
        $input[13]['updated_at'] = date('Y-m-d H:i:s');

        $input[14]['id_kualitas'] = 3;
        $input[14]['kode'] = 'PdE';
        $input[14]['name'] = 'Pendel Error';
        $input[14]['created_by'] = 1;
        $input[14]['created_at'] = date('Y-m-d H:i:s');
        $input[14]['updated_at'] = date('Y-m-d H:i:s');

        $input[15]['id_kualitas'] = 3;
        $input[15]['kode'] = 'Oli';
        $input[15]['name'] = 'Kena Oli';
        $input[15]['created_by'] = 1;
        $input[15]['created_at'] = date('Y-m-d H:i:s');
        $input[15]['updated_at'] = date('Y-m-d H:i:s');

        $input[16]['id_kualitas'] = 3;
        $input[16]['kode'] = 'Bl';
        $input[16]['name'] = 'Berlubang';
        $input[16]['created_by'] = 1;
        $input[16]['created_at'] = date('Y-m-d H:i:s');
        $input[16]['updated_at'] = date('Y-m-d H:i:s');

        MappingKualitas::insert($input);
    }
}
