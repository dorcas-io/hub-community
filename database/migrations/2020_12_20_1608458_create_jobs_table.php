<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('queue');
            $table->tinyInteger('attempts',);
            $table->integer('reserved_at',)->unsigned()->nullable();
            $table->integer('available_at',)->unsigned();
            $table->integer('created_at',)->unsigned();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}