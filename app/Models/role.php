<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class role extends Model
{
    use HasFactory;

	protected $fillable = [
        'name',
        'color',
        'can_add_reservation',
        'can_force_limit',
        'can_delete_reservations',
		'can_enter_admin_panel',
		'max_reservation_length',
    ];



	public function users ()
	{
		return $this -> hasMany (User::class);
	}
}
