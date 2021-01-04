<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {

			$table->bigIncrements('id');
			$table->char('uuid',50);
			$table->char('reg_number',30)->nullable();
			$table->char('name',100);
			$table->char('phone',30)->nullable();
			$table->char('email',200)->nullable();
			$table->string('website',100)->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}