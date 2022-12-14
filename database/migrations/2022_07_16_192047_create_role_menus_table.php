<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_role');
            $table->integer('id_menu');
            $table->integer('ucc');
            $table->integer('ucu');
            $table->integer('ucd');
            $table->string('uuid')->unique();
            $table->timestamps();
            $table->index(['id_role']);
            $table->index(['id_menu']);
            $table->unique(['id_role','id_menu']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_menus');
    }
}
