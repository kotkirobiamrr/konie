<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class settings extends Model
{
    use HasFactory;

	protected $fillable = [
        'opening_hour',
        'closing_hour',
        'allow_changing_username',
		'contact_info',
    ];

	public function get_working_hours ()
	{
		return array ($this -> opening_hour, $this -> closing_hour);
	}
}
