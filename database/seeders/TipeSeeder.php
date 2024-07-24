<?php

namespace Database\Seeders;

use App\Models\Tipe;
use Illuminate\Database\Seeder;

class TipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = "Benang";
        $input[0]['created_at'] = now();
        $input[0]['updated_at'] = now();

        $input[1]['name'] = "Chemical";
        $input[1]['created_at'] = now();
        $input[1]['updated_at'] = now();

        $input[2]['name'] = "Beam Lusi";
        $input[2]['created_at'] = now();
        $input[2]['updated_at'] = now();

        $input[3]['name'] = "Beam Songket";
        $input[3]['created_at'] = now();
        $input[3]['updated_at'] = now();

        $input[4]['name'] = "Pakan";
        $input[4]['created_at'] = now();
        $input[4]['updated_at'] = now();

        $input[5]['name'] = "Leno";
        $input[5]['created_at'] = now();
        $input[5]['updated_at'] = now();

        $input[6]['name'] = "Sarung";
        $input[6]['created_at'] = now();
        $input[6]['updated_at'] = now();

        $input[7]['name'] = "Zat Pembantu";
        $input[7]['created_at'] = now();
        $input[7]['updated_at'] = now();

        Tipe::insert($input);
    }
}
