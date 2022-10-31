<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            //
            $table->integer('invoice_number')->nullable();
            $table->string('uuid')->nullable();
            $table->integer('load_number')->nullable();
            $table->integer('order_number')->nullable();
            $table->string('cust_ref')->nullable();
            $table->integer('total')->nullable();
            $table->string('coin_type')->nullable();
            $table->string('error_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            //
        });
    }
}
