<?php

namespace Database\Seeders;

use App\Models\Gudang;
use Illuminate\Database\Seeder;

class GudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = 'Gudang Logistik';
        $input[0]['kode'] = 'GPB';
        $input[0]['tipe'] = 'SIMPAN';
        $input[0]['created_at'] = date('Y-m-d H:i:s');
        $input[0]['updated_at'] = date('Y-m-d H:i:s');

        $input[1]['name'] = 'Gudang Dyeing';
        $input[1]['kode'] = 'GD';
        $input[1]['tipe'] = 'PROSES';
        $input[1]['created_at'] = date('Y-m-d H:i:s');
        $input[1]['updated_at'] = date('Y-m-d H:i:s');

        $input[2]['name'] = 'Gudang Warping';
        $input[2]['kode'] = 'GW';
        $input[2]['tipe'] = 'PROSES';
        $input[2]['created_at'] = date('Y-m-d H:i:s');
        $input[2]['updated_at'] = date('Y-m-d H:i:s');

        $input[3]['name'] = 'Gudang Weaving';
        $input[3]['kode'] = 'GW';
        $input[3]['tipe'] = 'PROSES';
        $input[3]['created_at'] = date('Y-m-d H:i:s');
        $input[3]['updated_at'] = date('Y-m-d H:i:s');

        $input[4]['name'] = 'Gudang Inspekting';
        $input[4]['kode'] = 'GI';
        $input[4]['tipe'] = 'PROSES';
        $input[4]['created_at'] = date('Y-m-d H:i:s');
        $input[4]['updated_at'] = date('Y-m-d H:i:s');

        $input[5]['name'] = 'Gudang Finishing';
        $input[5]['kode'] = 'GF';
        $input[5]['tipe'] = 'PROSES';
        $input[5]['created_at'] = date('Y-m-d H:i:s');
        $input[5]['updated_at'] = date('Y-m-d H:i:s');

        $input[6]['name'] = 'Gudang Pakan';
        $input[6]['kode'] = 'GF';
        $input[6]['tipe'] = 'PROSES';
        $input[6]['created_at'] = date('Y-m-d H:i:s');
        $input[6]['updated_at'] = date('Y-m-d H:i:s');

        Gudang::insert($input);
    }
}
