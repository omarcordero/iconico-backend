<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Menu Items and Categories relation table
         */
        Schema::create('menu_items_categories', function (Blueprint $table) {
            $table->integer('menu_item_id')->unsigned();
            $table->integer('category_id')->unsigned();

            /*
             * Add Foreign/Unique/Index
             */
            $table->foreign('menu_item_id', 'foreign_pivot_menu_item_category')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade');

            $table->foreign('category_id', 'foreign_pivot_category_menu_item')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->unique(['menu_item_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items_categories', function (Blueprint $table) {
            $table->dropForeign('foreign_pivot_menu_item_category');
            $table->dropForeign('foreign_pivot_category_menu_item');
        });

        /*
         * Drop tables
         */
        Schema::dropIfExists('menu_items_categories');
    }
}
