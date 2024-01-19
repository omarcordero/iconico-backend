<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('referrer')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('referrer', 'refer_foreign_referrer')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('user_id', 'refer_foreign_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refer', function (Blueprint $table) {
            $table->dropForeign('refer_foreign_referrer');
            $table->dropForeign('refer_foreign_user');
        });

        Schema::dropIfExists('refer');
    }
}
