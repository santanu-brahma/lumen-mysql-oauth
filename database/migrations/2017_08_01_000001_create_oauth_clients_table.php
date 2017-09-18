<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

#use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id', 25);
            $table->string('client_secret', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oauth_clients');
    }
}
