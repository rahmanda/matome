<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnactmentDocsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enactments', function(Blueprint $table) {

			$table->increments('id');
			$table->integer('docId');
			$table->string('type');
			$table->string('number');
			$table->date('publishedDate');
			$table->string('identifier')->default('');
			$table->timestamps();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('enactments');
	}

}
