<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs', function(Blueprint $table) {

			$table->increments('id');
			$table->string('filename')->default('');
			$table->string('originalFilename')->default('');
			$table->integer('creatorId');
			$table->string('title')->default('');
			$table->string('identifier')->default('');
			$table->string('subject')->default('');
			$table->string('number')->default('');
			$table->string('docType')->default('');
			$table->mediumText('description')->default('');
			$table->date('publishedDate')->default('0000-00-00');
			$table->date('validDate')->default('0000-00-00');
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
		Schema::drop('docs');
	}

}
