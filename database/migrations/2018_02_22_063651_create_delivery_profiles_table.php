<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_online')->default(false);
            $table->decimal('longitude', 15, 7)->nullable();
            $table->decimal('latitude', 15, 7)->nullable();
            $table->integer('user_id')->unsigned();
            $table->boolean('assigned')->default(false);
            $table->timestamps();

            $table->foreign('user_id', 'foreign_delivery_user')
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
        Schema::dropIfExists('delivery_profiles');
    }
}
