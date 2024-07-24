<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['name'] = 'superadmin';
        $input[0]['nickname'] = 'Superadmin';
        $input[0]['email'] = 'superadmin@gmail.com';
        $input[0]['roles_id'] = 1;
        $input[0]['password'] = Hash::make('superadmin');
        $input[0]['created_at'] = date('Y-m-d H:i:s');
        $input[0]['updated_at'] = date('Y-m-d H:i:s');

        $input[1]['name'] = 'operatordyeing';
        $input[1]['nickname'] = 'OP Dyeing';
        $input[1]['email'] = 'operatordyeing@gmail.com';
        $input[1]['roles_id'] = 2;
        $input[1]['password'] = Hash::make('operatordyeing');
        $input[1]['created_at'] = date('Y-m-d H:i:s');
        $input[1]['updated_at'] = date('Y-m-d H:i:s');

        $input[2]['name'] = 'operatorpreparatory';
        $input[2]['nickname'] = 'OP Preparatory';
        $input[2]['email'] = 'operatorpreparatory@gmail.com';
        $input[2]['roles_id'] = 3;
        $input[2]['password'] = Hash::make('operatorpreparatory');
        $input[2]['created_at'] = date('Y-m-d H:i:s');
        $input[2]['updated_at'] = date('Y-m-d H:i:s');

        $input[3]['name'] = 'operatorweaving';
        $input[3]['nickname'] = 'OP Weaving';
        $input[3]['email'] = 'operatorweaving@gmail.com';
        $input[3]['roles_id'] = 4;
        $input[3]['password'] = Hash::make('operatorweaving');
        $input[3]['created_at'] = date('Y-m-d H:i:s');
        $input[3]['updated_at'] = date('Y-m-d H:i:s');

        $input[4]['name'] = 'operatorinspekting';
        $input[4]['nickname'] = 'OP Inspekting';
        $input[4]['email'] = 'operatorinspekting@gmail.com';
        $input[4]['roles_id'] = 5;
        $input[4]['password'] = Hash::make('operatorweaving');
        $input[4]['created_at'] = date('Y-m-d H:i:s');
        $input[4]['updated_at'] = date('Y-m-d H:i:s');

        $input[5]['name'] = 'operatorfinishing';
        $input[5]['nickname'] = 'OP Finishing';
        $input[5]['email'] = 'operatorfinishing@gmail.com';
        $input[5]['roles_id'] = 6;
        $input[5]['password'] = Hash::make('operatorweaving');
        $input[5]['created_at'] = date('Y-m-d H:i:s');
        $input[5]['updated_at'] = date('Y-m-d H:i:s');

        $input[6]['name'] = 'operatorlogistik';
        $input[6]['nickname'] = 'OP Logistik';
        $input[6]['email'] = 'operatorlogistik@gmail.com';
        $input[6]['roles_id'] = 7;
        $input[6]['password'] = Hash::make('operatorlogistik');
        $input[6]['created_at'] = date('Y-m-d H:i:s');
        $input[6]['updated_at'] = date('Y-m-d H:i:s');

        $input[7]['name'] = 'validator';
        $input[7]['nickname'] = 'Validator';
        $input[7]['email'] = 'validator@gmail.com';
        $input[7]['roles_id'] = 8;
        $input[7]['password'] = Hash::make('validator');
        $input[7]['created_at'] = date('Y-m-d H:i:s');
        $input[7]['updated_at'] = date('Y-m-d H:i:s');

        $input[8]['name'] = 'userinformasi';
        $input[8]['nickname'] = 'User Informasi';
        $input[8]['email'] = 'userinformasi@gmail.com';
        $input[8]['roles_id'] = 9;
        $input[8]['password'] = Hash::make('userinformasi');
        $input[8]['created_at'] = date('Y-m-d H:i:s');
        $input[8]['updated_at'] = date('Y-m-d H:i:s');

        User::insert($input);
    }
}
