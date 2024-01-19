<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('tagline')->default('');
            $table->string('image_url', 500)->default('');
            $table->string('delivery_time')->default('');    // avg delivery time
            $table->integer('minimum_order')->unsigned()->default(200);
            $table->decimal('delivery_fee', 8, 2)->default(25);
            $table->string('details')->default('');
            $table->integer('delivery_limit')->default(8000);
            $table->string('area')->default('');
            $table->string('address')->default('');
            $table->decimal('longitude', 15, 7)->default(0.0);
            $table->decimal('latitude', 15, 7)->default(0.0);
            $table->boolean('preorder')->default(true);
            $table->boolean('serves_non_veg')->default(true);
            $table->integer('cost_for_two')->default(250);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->time('opens_at')->default("12:00:00");
            $table->time('closes_at')->default("23:00:00");
            $table->integer('owner_id')->unsigned();
            $table->timestamps();

            $table->foreign('owner_id', 'foreign_stores_user')
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
        Schema::dropIfExists('stores');
    }
}
