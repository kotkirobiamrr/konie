<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
			$table->smallInteger('opening_hour');
			$table->smallInteger('closing_hour');
			$table->boolean('allow_changing_username') -> default (1);
			$table->text('contact_info')->nullable()->default('');
            $table->timestamps();
        });


		DB::table('settings')->insert(
			array(
				array(
					'opening_hour' => 7,
					'closing_hour' => 21,
					'allow_changing_username' => 1,
					'contact_info' => 'kontakt',
				),
			)
		);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
