<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxAuthoritiesTable extends Migration
{
    public function up()
    {
        Schema::create('tax_authorities', function (Blueprint $table) {

		$table->increments(id)->unsigned();
		$table->string('authority_name');
		$table->enum('payment_mode',['Paystack','FlutterWave']);
		;
		$table->timestamp('created_at')->nullable()->default('NULL');
		$table->timestamp('updated_at')->nullable()->default('NULL');
		$table->primary('id');

        });
    }

    public function down()
    {
        Schema::dropIfExists('tax_authorities');
    }
}