<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

class CreateOauthAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('user_id');
			$table->string('client_id', 25);
            $table->string('access_token', 50);
			$table->bigInteger('expires');
			$table->string('scope', 100)->nullable();
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
        Schema::drop('oauth_accesses');
    }
}
