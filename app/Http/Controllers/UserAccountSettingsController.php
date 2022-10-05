<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use App\Models\User;
use App\Models\Settings;

class UserAccountSettingsController extends Controller
{

	private $validator_fields = [
		'email' => '"adres e-mail"',
		'telephone' => '"telefon"',
		'name' => '"nazwa użytkownika"',
		'current_password' => '"aktualne hasło"',
		'new_password2' => 'nowego hasła',
		'new_password1' => 'nowego hasła',
	];

	private $validator_messages = [
		'email.unique' => 'Użytkownik o podanym adresie e-mail już istnieje.',
		'name.unique' => 'Użytkownik o podanej nazwie już istnieje.',
		'new_password2.same' => 'Hasła nie są takie same.'
	];


    public function index()
    {
        $u = Auth::user();

		return view ('accountsettings', ['user' => $u, 'can_change_name' => Settings::find(1) -> allow_changing_username]);
    }

	////////////////////////////////////////
	//
	//	edycja ogólnych ustawień konta (nazwa użytkownika, telefon)

	public function edit_general(Request $request)
	{
		$u = Auth::user();

		$validator = Validator::make($request->all(), [
			"name" => 'sometimes|string|max:30|unique:users,name,'.$u->id,
			"telephone" => 'sometimes|max:13',
		],
		$this -> validator_messages,
		$this -> validator_fields);

		if ($validator->fails()) {
			return redirect()->route('accountsettings')->withErrors($validator)->withInput();
		}

		$validated = $validator->validated();

		// jesli zmiana nazwy i zmiana nazwy jest dopuszczona
		if (isset($request -> name) && Settings::find(1) -> allow_changing_username)
		{
			$u -> name = $request -> name;
		}
		if (isset($request -> telephone))
		{
			$u -> telephone = $request -> telephone;
		}

		$u -> save();
		return redirect()->route('accountsettings')->with('message', 'Dane zostały zmienione.');
	}

	//	edycja adresu e-mail

	public function edit_email (Request $request)
	{
		$u = Auth::user();

		$validator = Validator::make($request->all(), [
			'email_password' => 'required|string',
			'email' => 'required|string|email|max:255|unique:users,email,'.$u->id,
		],
		$this -> validator_messages,
		$this -> validator_fields);

		if ($validator->fails()) {
			return redirect()->route('accountsettings')->withErrors($validator)->withInput();
		}

		$validated = $validator->validated();

		//	sprawdzamy, czy hasło się zgadza
		if (Hash::check($request -> email_password, $u -> password))
		{
			$u -> email = $request -> email;
			$u -> save();
			return redirect()->route('accountsettings')->with('message', 'E-mail został zmieniony.');
		}
		else
		{
			return redirect()->route('accountsettings')->withErrors(['email_password' => 'Nieprawidłowe hasło.'])->withInput();
		}
	}

	//	edycja hasła

	public function edit_password (Request $request)
	{
		$u = Auth::user();

		$validator = Validator::make($request->all(), [
			'current_password' => 'required|string',
            'new_password1' => ['required', Password::defaults()],
            'new_password2' => 'required|same:new_password1',
		],
		$this -> validator_messages,
		$this -> validator_fields);

		if ($validator->fails()) {
			return redirect()->route('accountsettings')->withErrors($validator)->withInput();
		}

		$validated = $validator->validated();

		//	sprawdzamy, czy stare hasło się zgadza
		if (Hash::check($request -> current_password, $u -> password))
		{
			$u->password = Hash::make($request -> new_password1);
			$u -> save();
			return redirect()->route('accountsettings')->with('message', 'Hasło zostało zmienione');
		}
		else
		{
			return redirect()->route('accountsettings')->withErrors(['current_password' => 'Nieprawidłowe hasło.']);
		}

	}

	public function delete (Request $request)
	{
		$u = Auth::user();

		//	sprawdzamy, czy stare hasło się zgadza
		if (Hash::check($request -> delete_password, $u -> password))
		{
			$u -> delete();
			return redirect()->route('login');
		}
		else
		{
			return redirect()->route('accountsettings')->withErrors(['delete_password' => 'Nieprawidłowe hasło.']);
		}
	}
}
