<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminSettingsController extends Controller
{
    /**
     * w tabeli ustawień jest tylko jeden rekord....
     *
     */
	public function has_permissions()
	{
		return Auth::user() -> role -> can_enter_admin_panel == 1;
	}

    public function index()
    {
		if ($this -> has_permissions())
		{
			$s = Settings::find(1);

			return view('admin.settings', $s);
		}
		else
		{
			return redirect ('home');
		}
    }

    /**
     * edytuj ustawienia
     *
     */
    public function edit(Request $r)
    {
        if ($this -> has_permissions())
		{
			$fields = [
				'opening_hour' => '"godzina otwarcia"',
				'closing_hour' => '"godzina zamknięcia"',
			];

			$validator = Validator::make($r->all(), [
				"opening_hour" => 'required|integer|min:1|max:23',
				"closing_hour" => 'required|integer|min:1|max:23',
			],
			[],
			$fields);

			if ($validator->fails()) {
				return redirect()->route('admin_settings')->withErrors($validator)->withInput();
			}

			$validated = $validator->validated();


			$s = Settings::find(1);

			$s -> opening_hour = $r -> opening_hour;
			$s -> closing_hour = $r -> closing_hour;
			$s -> contact_info = $r -> contact_info;
			if (isset ($r -> allow_changing_username) && $r -> allow_changing_username == 'on')
			{
				$s -> allow_changing_username = 1;
			}
			else
			{
				$s -> allow_changing_username = 0;
			}
			$s -> save();
			return redirect() -> route('admin_settings')->with('message', 'Ustawienia serwisu zostały zmienione.');
		}
		else
		{
			return redirect ('home');
		}
    }

}
