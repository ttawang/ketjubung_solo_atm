<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = 'administrator';
        $input[0]['initial_name'] = 'Administrator';
        $input[0]['created_at'] = date('Y-m-d H:i:s');
        $input[0]['updated_at'] = date('Y-m-d H:i:s');

        $input[1]['name'] = 'dyeing';
        $input[1]['initial_name'] = 'OPD';
        $input[1]['created_at'] = date('Y-m-d H:i:s');
        $input[1]['updated_at'] = date('Y-m-d H:i:s');

        $input[2]['name'] = 'preparatory';
        $input[2]['initial_name'] = 'PR';
        $input[2]['created_at'] = date('Y-m-d H:i:s');
        $input[2]['updated_at'] = date('Y-m-d H:i:s');

        $input[3]['name'] = 'weaving';
        $input[3]['initial_name'] = 'OPW';
        $input[3]['created_at'] = date('Y-m-d H:i:s');
        $input[3]['updated_at'] = date('Y-m-d H:i:s');

        $input[4]['name'] = 'inspekting';
        $input[4]['initial_name'] = 'OPI';
        $input[4]['created_at'] = date('Y-m-d H:i:s');
        $input[4]['updated_at'] = date('Y-m-d H:i:s');

        $input[5]['name'] = 'finishing';
        $input[5]['initial_name'] = 'OPF';
        $input[5]['created_at'] = date('Y-m-d H:i:s');
        $input[5]['updated_at'] = date('Y-m-d H:i:s');

        $input[6]['name'] = 'logistik';
        $input[6]['initial_name'] = 'LO';
        $input[6]['created_at'] = date('Y-m-d H:i:s');
        $input[6]['updated_at'] = date('Y-m-d H:i:s');

        $input[7]['name'] = 'validator';
        $input[7]['initial_name'] = 'VLD';
        $input[7]['created_at'] = date('Y-m-d H:i:s');
        $input[7]['updated_at'] = date('Y-m-d H:i:s');

        $input[8]['name'] = 'user informasi';
        $input[8]['initial_name'] = 'UIN';
        $input[8]['created_at'] = date('Y-m-d H:i:s');
        $input[8]['updated_at'] = date('Y-m-d H:i:s');
        Role::insert($input);
    }
}
