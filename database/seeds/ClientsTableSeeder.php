<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->insert([
            'client_id' => '1234567890',
            'client_secret' => 'a83nWjtubZ8m2vlCibRIfIASZbxOwaiU'
        ]);
    }
}
