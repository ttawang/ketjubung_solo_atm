<?php

namespace Database\Seeders;

use App\Models\MappingMenu;
use Illuminate\Database\Seeder;

class MappingMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input[0]['roles_id'] = 1;
        $input[0]['menus_id'] = 1;
        $input[0]['created_at'] = now();
        $input[0]['updated_at'] = now();

        $input[1]['roles_id'] = 1;
        $input[1]['menus_id'] = 2;
        $input[1]['created_at'] = now();
        $input[1]['updated_at'] = now();

        $input[2]['roles_id'] = 1;
        $input[2]['menus_id'] = 3;
        $input[2]['created_at'] = now();
        $input[2]['updated_at'] = now();

        $input[3]['roles_id'] = 1;
        $input[3]['menus_id'] = 4;
        $input[3]['created_at'] = now();
        $input[3]['updated_at'] = now();

        $input[4]['roles_id'] = 1;
        $input[4]['menus_id'] = 5;
        $input[4]['created_at'] = now();
        $input[4]['updated_at'] = now();

        $input[5]['roles_id'] = 1;
        $input[5]['menus_id'] = 6;
        $input[5]['created_at'] = now();
        $input[5]['updated_at'] = now();

        $input[6]['roles_id'] = 1;
        $input[6]['menus_id'] = 7;
        $input[6]['created_at'] = now();
        $input[6]['updated_at'] = now();

        $input[7]['roles_id'] = 1;
        $input[7]['menus_id'] = 8;
        $input[7]['created_at'] = now();
        $input[7]['updated_at'] = now();

        $input[8]['roles_id'] = 1;
        $input[8]['menus_id'] = 9;
        $input[8]['created_at'] = now();
        $input[8]['updated_at'] = now();

        $input[9]['roles_id'] = 1;
        $input[9]['menus_id'] = 10;
        $input[9]['created_at'] = now();
        $input[9]['updated_at'] = now();

        $input[10]['roles_id'] = 1;
        $input[10]['menus_id'] = 11;
        $input[10]['created_at'] = now();
        $input[10]['updated_at'] = now();

        $input[11]['roles_id'] = 1;
        $input[11]['menus_id'] = 14;
        $input[11]['created_at'] = now();
        $input[11]['updated_at'] = now();

        $input[12]['roles_id'] = 1;
        $input[12]['menus_id'] = 15;
        $input[12]['created_at'] = now();
        $input[12]['updated_at'] = now();

        $input[13]['roles_id'] = 1;
        $input[13]['menus_id'] = 16;
        $input[13]['created_at'] = now();
        $input[13]['updated_at'] = now();

        $input[14]['roles_id'] = 1;
        $input[14]['menus_id'] = 17;
        $input[14]['created_at'] = now();
        $input[14]['updated_at'] = now();

        $input[15]['roles_id'] = 1;
        $input[15]['menus_id'] = 18;
        $input[15]['created_at'] = now();
        $input[15]['updated_at'] = now();

        $input[16]['roles_id'] = 1;
        $input[16]['menus_id'] = 19;
        $input[16]['created_at'] = now();
        $input[16]['updated_at'] = now();

        $input[17]['roles_id'] = 1;
        $input[17]['menus_id'] = 22;
        $input[17]['created_at'] = now();
        $input[17]['updated_at'] = now();

        $input[18]['roles_id'] = 1;
        $input[18]['menus_id'] = 28;
        $input[18]['created_at'] = now();
        $input[18]['updated_at'] = now();

        $input[19]['roles_id'] = 1;
        $input[19]['menus_id'] = 29;
        $input[19]['created_at'] = now();
        $input[19]['updated_at'] = now();

        $input[20]['roles_id'] = 1;
        $input[20]['menus_id'] = 32;
        $input[20]['created_at'] = now();
        $input[20]['updated_at'] = now();

        $input[21]['roles_id'] = 1;
        $input[21]['menus_id'] = 35;
        $input[21]['created_at'] = now();
        $input[21]['updated_at'] = now();

        $input[22]['roles_id'] = 1;
        $input[22]['menus_id'] = 36;
        $input[22]['created_at'] = now();
        $input[22]['updated_at'] = now();

        $input[23]['roles_id'] = 1;
        $input[23]['menus_id'] = 45;
        $input[23]['created_at'] = now();
        $input[23]['updated_at'] = now();

        MappingMenu::insert($input);
    }
}
