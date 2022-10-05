<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
			$table->string('name');		//nazwa placu
			$table->integer('horse_limit')->unsigned(); //ile koni może wejść maksymalnie
			$table->integer('display_order')->default(0); //kolejność, wg której wyświetlany będzie dany plac
            $table->timestamps();
        });

		DB::table('areas')->insert (
			array (
				'name' => 'Mały plac',
				'horse_limit' => 2,
				'display_order' => 1,
			)
		);
		DB::table('areas')->insert (
			array (
				'name' => 'Duży plac',
				'horse_limit' => 6,
				'display_order' => 5,
			)
		);
		DB::table('areas')->insert (
			array (
				'name' => 'Hala',
				'horse_limit' => 1,
				'display_order' => 10,
			)
		);
    }


	public function reservations ()
	{
		$this -> hasMany (Reservation::class);
	}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('areas');
    }

}
