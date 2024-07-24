<?php

namespace Database\Seeders;

use App\Models\Pekerja;
use Illuminate\Database\Seeder;

class PekerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['id_group'] = 1;
        $input[0]['no_register'] = 'P001';
        $input[0]['name'] = 'Pekerja 1';
        $input[0]['no_hp'] = '08977917823';
        $input[0]['created_by'] = 1;
        $input[0]['created_at'] = date('Y-m-d H:i:s');
        $input[0]['updated_at'] = date('Y-m-d H:i:s');

        $input[1]['id_group'] = 2;
        $input[1]['no_register'] = 'P002';
        $input[1]['name'] = 'Pekerja 2';
        $input[1]['no_hp'] = '08977917823';
        $input[1]['created_by'] = 1;
        $input[1]['created_at'] = date('Y-m-d H:i:s');
        $input[1]['updated_at'] = date('Y-m-d H:i:s');

        $input[2]['id_group'] = 3;
        $input[2]['no_register'] = 'P003';
        $input[2]['name'] = 'Pekerja 3';
        $input[2]['no_hp'] = '08977917823';
        $input[2]['created_by'] = 1;
        $input[2]['created_at'] = date('Y-m-d H:i:s');
        $input[2]['updated_at'] = date('Y-m-d H:i:s');

        Pekerja::insert($input);
    }
}
