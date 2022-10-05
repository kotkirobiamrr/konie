<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
			$table->datetime('start');	//czas początku rezerwacji
			$table->datetime('end');	//czas końca rezerwacji
			$table->integer('horses_count')->unsigned();	//ile koni w ramach rezerwacji
			$table->boolean('exclusive')->default(0);		//czy na wyłączność
			$table->bigInteger('user_id')->unsigned();		//użytkownik rezerwujący
			$table->bigInteger('area_id')->unsigned();		//na którym placu
			$table->boolean('show_comment')->default(0);		//pokaż komentarz w terminarzu
			$table->string('comment')->default('');			//komentarz

			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');	//klucz obcy z użytkowników
			$table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');	//klucz obcy z przestrzeni treningowych

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
		$table->dropForeign(['area_id']);
		$table->dropForeign(['user_id']);

        Schema::dropIfExists('reservations');
    }
}
