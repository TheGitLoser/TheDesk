<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('business_plan')->insert(
            [
                'unique_id' => '52K3J99W9JSWSWGCK',
                'name' => 'HKPU',
                'profile' => 'The Hong Kong Polytechnic University'
            ]
        );

        DB::table('business_user')->insert(
            [
                'business_plan_id' => '1',
                'user_id' => '2'
            ]
        );

        DB::table('status')->insert(
            [
                ['id' => '1', 'status_name' => 'Normal'],
                ['id' => '2', 'status_name' => 'Pending'],
                ['id' => '3', 'status_name' => 'Expired'],
                ['id' => '4', 'status_name' => 'Deleted']
            ]
        );

        DB::table('user')->insert(
            [
                [
                    'unique_id' => '1KDVJS5MU44KWWOCSO',
                    'name' => 'Admin',
                    'display_id' => 'systemadmin',
                    'email' => 'admin@example.com',
                    'password' => '$2y$11$ZkuaFJ7rgmmSVAsn4D7XnudVhPWk9DW3I0tg0baa.KbmPlWwdBSN6',
                    'type' => 'admin',
                    'phone' => '12345678'
                ],
                [
                    'unique_id' => '3U5GVI06RKOW8WOKC',
                    'name' => 'PolyU admin',
                    'display_id' => 'polyuadmin',
                    'email' => 'polyu@example.com',
                    'password' => '$2y$11$ZkuaFJ7rgmmSVAsn4D7XnudVhPWk9DW3I0tg0baa.KbmPlWwdBSN6',
                    'type' => 'business admin',
                    'phone' => '12345678'
                ]
            ]
        );
    }
}
