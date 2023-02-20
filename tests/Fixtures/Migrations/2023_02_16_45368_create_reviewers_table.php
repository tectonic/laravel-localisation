<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reviewers', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('link_id')->index();
            $table->timestamps();

            $table->foreign('link_id')->references('id')->on('links');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reviewers');
	}

}
