<?php

use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->insert([
            [
                'id'  => 1,
                'pid' => 0,
                'title' => '系统管理',
                'sort' => 0,
                'url' => '',
            ],
            [
                'id'  => 2,
                'pid' => 0,
                'title' => '财务功能',
                'sort' => 0,
                'url' => '',
            ],
            [
                'id'  => 3,
                'pid' => 1,
                'title' => '用户管理',
                'sort' => 0,
                'url' => '/user/index',
            ],
            [
                'id'  => 4,
                'pid' => 2,
                'title' => '业务对账',
                'sort' => 0,
                'url' => '/finance/account-record',
            ],
        ]);
    }
}
