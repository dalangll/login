<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username',50);
            $table->char('mobile',11)->unique();
            $table->char('email',50)->unique();
            $table->boolean('status')->default(false);
            $table->boolean('lock')->default(false);
            $table->string('password');
            $table->string('uuid',255);
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
        Schema::dropIfExists('member');
    }
}
