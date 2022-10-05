<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('color');
			$table->boolean('can_add_reservation');		//czy może dodać rezerwację
			$table->boolean('can_force_limit');			//czy może przekroczyć limit rezerwacji
			$table->boolean('can_reserve_exclusively');	//czy może zarezerwować na wyłączność
			$table->boolean('can_delete_reservations');	//czy może usuwać rezerwacje
			$table->boolean('can_enter_admin_panel');	//czy może zarządzać systemem
			$table->integer('max_reservation_length');	//maksymalna długość pojedynczej rezerwacji w minutach (0 - bez limitu)
            $table->timestamps();

        });

		DB::table('roles')->insert(
			array(
				array(
					'name' => 'Pensjonariusz',
					'color' => '#28a745',
					'can_add_reservation' => true,
					'can_force_limit' => false,
					'can_reserve_exclusively' => false,
					'can_delete_reservations' => false,
					'can_enter_admin_panel' => false,
					'max_reservation_length' => 90
				),
				array(
					'name' => 'Właściciel',
					'color' => '#dc3545',
					'can_add_reservation' => true,
					'can_force_limit' => true,
					'can_reserve_exclusively' => true,
					'can_delete_reservations' => true,
					'can_enter_admin_panel' => true,
					'max_reservation_length' => 0

				),
				array(
					'name' => 'Instruktor',
					'color' => '#007bff',
					'can_add_reservation' => true,
					'can_force_limit' => true,
					'can_reserve_exclusively' => true,
					'can_delete_reservations' => false,
					'can_enter_admin_panel' => false,
					'max_reservation_length' => 120

				)
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
        Schema::dropIfExists('role');
    }
}
