<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Store and Categories relation table
         */
        Schema::create('stores_categories', function (Blueprint $table) {
            $table->integer('store_id')->unsigned();
            $table->integer('category_id')->unsigned();

            /*
             * Add Foreign/Unique/Index
             */
            $table->foreign('store_id', 'foreign_pivot_store_category')
                ->references('id')
                ->on('stores')
                ->onDelete('cascade');

            $table->foreign('category_id', 'foreign_pivot_category_store')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->unique(['store_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores_categories', function (Blueprint $table) {
            $table->dropForeign('foreign_pivot_store_category');
            $table->dropForeign('foreign_pivot_category_store');
        });

        /*
         * Drop tables
         */
        Schema::dropIfExists('stores_categories');
    }
}
