<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Area extends Model
{
    use HasFactory;

	protected $fillable = [
        'name',
        'horse_limit',
        'display_order',
    ];


	//rezerwacje na tym placu
	public function reservations ()
	{
		return $this -> hasMany (Reservation::class);
	}

	/*
	* pobierz rezerwacje z danego dnia
	* @param Carbon::date $date
	* @return d
	*/
	public function get_reservations_on_day ($date)
	{


		//$reservations =
			// $this -> reservations()
			// -> join ('users', 'reservations.user_id', '=', 'users.id')
			// -> join ('roles', 'roles.id', '=', 'users.role_id')
			// -> select ('reservations.*', 'users.name as user_name', 'roles.color as user_color')
			// -> where ('start', '>=', Carbon::create($day))
			// -> where ('end', '<', $day -> addDay())
			// -> orderBy ('start', 'asc');

			$reservations = $this -> reservations()
			 	-> with ('user:id,name,role_id', 'user.role:id,color')
				-> where ('start', '>=', $date)
				-> where ('end', '<', Carbon::create($date) -> addDay())
				-> orderBy ('start', 'asc')
				-> get();


		return $reservations;
	}
}
