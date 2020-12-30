<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestDeploysTable extends Migration
{
    public function up()
    {
        Schema::create('test_deploys', function (Blueprint $table) {

		$table->$table->primary('id');
		$table->char('uuid',50);
		$table->string('tests',100)->nullable()->default('NULL');
		$table->timestamp('deleted_at')->nullable()->default('NULL');
		$table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('test_deploys');
    }
}