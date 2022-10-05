<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            //$table->timestamp('email_verified_at')->nullable();
            $table->string('password');
			$table->string('telephone')->nullable();
			$table->integer('horses_count')->unsigned()->default(1);
			//$table->foreignId('role_id')->constrained()->onUpdate('cascade')->onDelete('cascade');

			$table->bigInteger('role_id')->unsigned()->nullable()->default(1);
            $table->rememberToken();
            $table->timestamps();

			$table->foreign('role_id')->references('id')->on('roles');	//relacja z tabelą z rolami


        });

		DB::table('users')->insert (
			array (
				'name' => 'mateusz',
				'email' => 'w@jcha.pl',
				'password' => '$2y$10$7EhNQArMTu.mHpFpl820LeG6Y/owRUvVf5WSTOT/upFAMZ.IM1F5S'

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
		$table->dropForeign(['role_id']);
        Schema::dropIfExists('users');
    }
}
