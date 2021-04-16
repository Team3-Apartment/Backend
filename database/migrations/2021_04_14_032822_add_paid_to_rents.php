<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidToRents extends Migration
{
	public function up()
	{
		Schema::table('rents', function (Blueprint $table) {
			$table->boolean('paid')->default(0);
		});
	}

	public function down()
	{
		Schema::table('rents', function (Blueprint $table) {
			$table->dropColumn(['paid']);
		});
	}
}
