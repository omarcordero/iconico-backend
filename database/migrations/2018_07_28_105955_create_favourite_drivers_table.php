<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFavouriteDriversTable extends Migration
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
        Schema::create('favourite_drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_profile_id')->unsigned();
            $table->integer('store_id')->unsigned();
            $table->timestamps();

            /*
             * Add Foreign/Unique/Index
             */
            $table->foreign('delivery_profile_id', 'foreign_favourite_drivers_delivery')
                ->references('id')
                ->on('delivery_profiles')
                ->onDelete('cascade');

            $table->foreign('store_id', 'foreign_favourite_drivers_store')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade');

            $table->unique(['delivery_profile_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favourite_drivers', function (Blueprint $table) {
            $table->dropForeign('foreign_favourite_drivers_delivery');
            $table->dropForeign('foreign_favourite_drivers_store');
        });

        /*
         * Drop tables
         */
        Schema::dropIfExists('favourite_drivers');
    }
}
