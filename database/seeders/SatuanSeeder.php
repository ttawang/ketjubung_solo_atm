<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = 'cones';
        $input[0]['created_at'] = now();
        $input[0]['updated_at'] = now();

        $input[1]['name'] = 'kg';
        $input[1]['created_at'] = now();
        $input[1]['updated_at'] = now();

        $input[2]['name'] = 'beam';
        $input[2]['created_at'] = now();
        $input[2]['updated_at'] = now();

        $input[3]['name'] = 'pcs';
        $input[3]['created_at'] = now();
        $input[3]['updated_at'] = now();

        $input[4]['name'] = 'gram';
        $input[4]['created_at'] = now();
        $input[4]['updated_at'] = now();

        $input[5]['name'] = 'meter';
        $input[5]['created_at'] = now();
        $input[5]['updated_at'] = now();

        Satuan::insert($input);
    }
}
