<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

	protected $fillable = [
        'start',
        'end',
        'horses_count',
		'exclusive',
		'user_id',
		'area_id',
		'comment',
    ];


	public function area()
	{
		return $this->belongsTo(Area::class);
	}
	public function user()
	{
		return $this->belongsTo(User::class);
	}
	public function role()
	{
		return $this->user->role;
	}


}
