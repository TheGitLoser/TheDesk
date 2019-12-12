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
                'unique_id' => 'sjhkfrot3524se',
                'company_name' => 'Business Name',
                'status' => '1'
            ]
        );

        DB::table('business_user')->insert(
            [
                'business_plan_id' => '1',
                'user_id' => '2',
                'status' => '1'
            ]
        );

        DB::table('status')->insert(
            [
                ['id' => '1', 'status_name' => 'Normal'],
                ['id' => '2', 'status_name' => 'Pending'],
                ['id' => '3', 'status_name' => 'Expired'],
                ['id' => '4', 'status_name' => 'Deleted'],
                ['id' => '5', 'status_name' => 'Message seen'],
                ['id' => '6', 'status_name' => 'Message not seen']
            ]
        );

        DB::table('user')->insert(
            [
                [
                    'unique_id' => 'owirje34rea3q0',
                    'name' => 'indi name',
                    'display_id' => 'Indi. name',
                    'email' => 'test@example.com',
                    'password' => '$2y$11$ZkuaFJ7rgmmSVAsn4D7XnudVhPWk9DW3I0tg0baa.KbmPlWwdBSN6',
                    'type' => 'indi',
                    'phone' => '12345678',
                    'status' => '1'
                ],
                [
                    'unique_id' => 'dfghhroegdf',
                    'name' => 'business user',
                    'display_id' => 'Business user',
                    'email' => 'buesinessuser@example.com',
                    'password' => '$2y$11$ZkuaFJ7rgmmSVAsn4D7XnudVhPWk9DW3I0tg0baa.KbmPlWwdBSN6',
                    'type' => 'business',
                    'phone' => '12345678',
                    'status' => '1'
                ],
                [
                    'unique_id' => 'ajkdh',
                    'name' => 'Tommy',
                    'display_id' => 'Tommy',
                    'email' => 'tommy@example.com',
                    'password' => '$2y$11$ZkuaFJ7rgmmSVAsn4D7XnudVhPWk9DW3I0tg0baa.KbmPlWwdBSN6',
                    'type' => 'indi',
                    'phone' => '12345678',
                    'status' => '1'
                ]
            ]
        );
    }
}
