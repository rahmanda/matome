<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servers', function(Blueprint $table) {

			$table->increments('id');
			$table->integer('userId');
			$table->string('hostname')->nullable();
			$table->string('address')->nullable();
			$table->string('directory')->nullable();
			$table->string('jobname')->nullable();
			$table->string('schedule')->nullable();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('servers');
	}

}
