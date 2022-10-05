<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use App\Models\settings;

class Schedule
{

	public $date;
	public $schedule = array();
	public $user = array();
	public $working_hours = array();

	public function __construct ($usr, $date)
	{

		$this -> date = $date;

		$this -> user['name'] = $usr -> name;
		$this -> user['horses_count'] = $usr -> horses_count;
		$this -> user['permissions'][] = $usr -> role -> can_add_reservation;
		$this -> user['permissions'][] = $usr -> role -> can_force_limit;
		$this -> user['permissions'][] = $usr -> role -> can_reserve_exclusively;
		$this -> user['permissions'][] = $usr -> role -> can_delete_reservations;
		$this -> user['permissions'][] = $usr -> role -> can_enter_admin_panel;
		$this -> user['permissions'][] = $usr -> role -> max_reservation_length;

	}
	public function to_json_string ()
	{

		$this -> date = $this -> date -> toString();
		return json_encode ($this);
	}

}
