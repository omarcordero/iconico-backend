<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFavouritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Store user's favourite store
         */
        Schema::create('favourites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('store_id')->unsigned();
            $table->timestamps();

            /*
             * Add Foreign/Unique/Index
             */
            $table->foreign('user_id', 'foreign_favourite_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('store_id', 'foreign_favourite_store')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade');

            $table->unique(['user_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favourites', function (Blueprint $table) {
            $table->dropForeign('foreign_favourite_user');
            $table->dropForeign('foreign_favourite_store');
        });

        /*
         * Drop tables
         */
        Schema::dropIfExists('favourites');
    }
}
