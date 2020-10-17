<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductForeignIdField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function(Blueprint $table)
        {
            $table->string('foreign_product_id')->after('article')->nullable();
            $table->boolean('active')->after('foreign_product_id')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function(Blueprint $table)
        {
            $table->dropColumn('foreign_product_id');
            $table->dropColumn('active');
        });
    }
}
