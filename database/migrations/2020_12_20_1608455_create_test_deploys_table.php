<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestDeploysTable extends Migration
{
    public function up()
    {
        Schema::create('test_deploys', function (Blueprint $table) {

		$table->integer('id')->primary()->unsigned();
		$table->char('uuid',50);
		$table->string('tests',100)->nullable();
		$table->timestamp('deleted_at')->nullable();
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('test_deploys');
    }
}