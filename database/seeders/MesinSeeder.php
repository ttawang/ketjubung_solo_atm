<?php

namespace Database\Seeders;

use App\Models\Mesin;
use Illuminate\Database\Seeder;

class MesinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = 'A';
        $input[0]['jenis'] = 'DYEING';
        $input[0]['created_by'] = 1;
        $input[0]['created_at'] = date('Y-m-d H:i:s');
        $input[0]['updated_at'] = date('Y-m-d H:i:s');

        $input[1]['name'] = 'E';
        $input[1]['jenis'] = 'DYEING';
        $input[1]['created_by'] = 1;
        $input[1]['created_at'] = date('Y-m-d H:i:s');
        $input[1]['updated_at'] = date('Y-m-d H:i:s');

        $input[2]['name'] = 'L';
        $input[2]['jenis'] = 'DYEING';
        $input[2]['created_by'] = 1;
        $input[2]['created_at'] = date('Y-m-d H:i:s');
        $input[2]['updated_at'] = date('Y-m-d H:i:s');

        $input[3]['name'] = 'O';
        $input[3]['jenis'] = 'DYEING';
        $input[3]['created_by'] = 1;
        $input[3]['created_at'] = date('Y-m-d H:i:s');
        $input[3]['updated_at'] = date('Y-m-d H:i:s');

        $input[4]['name'] = 'S';
        $input[4]['jenis'] = 'DYEING';
        $input[4]['created_by'] = 1;
        $input[4]['created_at'] = date('Y-m-d H:i:s');
        $input[4]['updated_at'] = date('Y-m-d H:i:s');

        $input[5]['name'] = 'A';
        $input[5]['jenis'] = 'WARPING';
        $input[5]['created_by'] = 1;
        $input[5]['created_at'] = date('Y-m-d H:i:s');
        $input[5]['updated_at'] = date('Y-m-d H:i:s');

        $input[6]['name'] = 'B';
        $input[6]['jenis'] = 'WARPING';
        $input[6]['created_by'] = 1;
        $input[6]['created_at'] = date('Y-m-d H:i:s');
        $input[6]['updated_at'] = date('Y-m-d H:i:s');

        $input[7]['name'] = 'C';
        $input[7]['jenis'] = 'WARPING';
        $input[7]['created_by'] = 1;
        $input[7]['created_at'] = date('Y-m-d H:i:s');
        $input[7]['updated_at'] = date('Y-m-d H:i:s');

        Mesin::insert($input);

        $loom = array('A', 'B', 'C', 'D', 'E', 'F');
        foreach ($loom as $i) {
            for ($j = 1; $j <= 22; $j++) {
                $data = [
                    'name' => $i.$j,
                    'jenis' => 'LOOM',
                    'created_by' => 1
                ];
                Mesin::insert($data);
            }
        }
    }
}
