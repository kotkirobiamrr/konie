<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

use App\Models\settings;
use App\Models\Area;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

		//najbliższa rezerwacja
		$upcoming = Auth::user() -> reservations ()
		-> where ('start', '>', now())
		-> orderBy ('start')
		-> first ();

		if (is_null ($upcoming))
		{
			$upcoming = 0;
		}
		else
		{
			$upcoming = $upcoming -> start;
		}

		$settings = settings::find(1);
		$areas = Area::select('id', 'name') -> orderBy ('display_order') -> get();

		return view('home', [
			'upcoming_timestamp' => $upcoming,
			'areas' => $areas,
			'user' => Auth::user(),
			'opening_hour' => $settings -> opening_hour,
			'closing_hour' => $settings -> closing_hour]);
    }
}
