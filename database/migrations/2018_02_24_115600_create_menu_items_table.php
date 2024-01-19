<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('detail');
            $table->string('specification');
            $table->string('image_url', 500);
            $table->double('price', 8,2);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_non_veg');
            $table->enum('status', ['pending', 'rejected', 'approved'])->default('approved');
            $table->integer('store_id')->unsigned();
            $table->timestamps();

            $table->foreign('store_id', 'foreign_item_store')
                ->references('id')
                ->on('stores')
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
        Schema::dropIfExists('menu_items');
    }
}
