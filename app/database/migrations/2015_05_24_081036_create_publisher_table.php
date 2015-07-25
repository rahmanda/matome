<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublisherTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('creators', function(Blueprint $table) {

			$table->increments('id');
			$table->integer('userId');
			$table->string('type');
			$table->string('administrativeLevel')->default('');
			$table->string('region')->default('');
			$table->string('name')->default('');
			$table->string('fields')->default('');
			$table->string('siteUrl')->default('');
			$table->tinyInteger('valid')->default(1);

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('creators');
	}

}
