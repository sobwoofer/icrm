<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedForeignOptionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_option', function(Blueprint $table)
        {
            $table->bigInteger('foreign_option_id')->unsigned()->index()->nullable();
        });

        Schema::table('price_option', function(Blueprint $table)
        {
            $table->foreign('foreign_option_id')->references('id')->on('foreign_option')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_option', function(Blueprint $table)
        {
            $table->dropForeign('price_option_foreign_option_id_foreign');
            $table->dropIndex('price_option_foreign_option_id_index');
            $table->dropColumn('foreign_option_id');
        });
    }
}
