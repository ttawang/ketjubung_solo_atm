<?php

namespace Database\Seeders;

use App\Models\Warna;
use Illuminate\Database\Seeder;

class WarnaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = "HITAM";
        $input[0]['alias'] = "IT";
        $input[0]['jenis'] = "SINGLE";
        $input[0]['created_by'] = 1;
        $input[0]['created_at'] = now();
        $input[0]['updated_at'] = now();

        $input[1]['name'] = "PUTIH";
        $input[1]['alias'] = "P";
        $input[1]['jenis'] = "SINGLE";
        $input[1]['created_by'] = 1;
        $input[1]['created_at'] = now();
        $input[1]['updated_at'] = now();

        $input[2]['name'] = "MERAH, KUNING, ORANGE";
        $input[2]['alias'] = "MKO";
        $input[2]['jenis'] = "KOMBINASI";
        $input[2]['created_by'] = 1;
        $input[2]['created_at'] = now();
        $input[2]['updated_at'] = now();

        Warna::insert($input);
    }
}
