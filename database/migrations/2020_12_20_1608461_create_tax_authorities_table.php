<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxAuthoritiesTable extends Migration
{
    public function up()
    {
        Schema::create('tax_authorities', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('authority_name');
            $table->enum('payment_mode',['Paystack','FlutterWave']);
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('tax_authorities');
    }
}