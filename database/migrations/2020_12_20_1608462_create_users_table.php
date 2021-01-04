<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

		$table->bigIncrements('id');
		$table->char('uuid',80);
		$table->bigInteger('company_id')->unsigned();
		$table->char('firstname',30);
		$table->char('lastname',30);
		$table->string('email',80);
		$table->string('password');
		$table->string('remember_token',100)->nullable();
		$table->enum('gender',['female','male'])->nullable();
		$table->char('phone',30)->nullable();
		$table->string('photo_url',300)->nullable();
		$table->tinyInteger('is_verified')->default(0);
		$table->tinyInteger('is_partner')->default(0);
		$table->tinyInteger('is_professional')->default(0);
		$table->integer('partner_id',)->unsigned()->nullable();
		$table->timestamp('deleted_at')->nullable();
		$table->timestamps();
		$table->foreign('company_id')->references('id')->on('companies');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}