<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

		$table->increments(id)->unsigned();
		$table->char('uuid',80);
		$table->integer('company_id',)->unsigned();
		$table->char('firstname',30);
		$table->char('lastname',30);
		$table->string('email',80);
		$table->string('password');
		$table->string('remember_token',100)->nullable()->default('NULL');
		$table->enum('gender',['female','male'])->nullable()->default('NULL');
		$table->char('phone',30)->nullable()->default('NULL');
		$table->string('photo_url',300)->nullable()->default('NULL');
		$table->tinyInteger('is_verified',)->default('0');
		$table->tinyInteger('is_partner',)->default('0');
		$table->tinyInteger('is_professional',)->default('0');
		$table->integer('partner_id',)->unsigned()->nullable()->default('NULL');
		$table->timestamp('deleted_at')->nullable()->default('NULL');
		$table->timestamp('created_at')->nullable()->default('NULL');
		$table->timestamp('updated_at')->nullable()->default('NULL');
		$table->primary('id');
		$table->foreign('company_id')->references('id')->on('companies');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}