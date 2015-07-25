<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnappliedEffectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('unappliedEffects', function(Blueprint $table) {

			$table->increments('id');
			$table->integer('docId');
			$table->string('docNumber');
			$table->string('status');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('unappliedEffects');
	}

}
