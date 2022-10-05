<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\role;

class AdminUsersController extends Controller
{
	public function __construct()
    {

	}

	public function has_permissions()
	{
		return Auth::user() -> role -> can_enter_admin_panel == 1;
	}
    /**
     * 	wyświetl użytkowników
     *
     */
    public function index()
    {
        //
		if ($this -> has_permissions())
		{
			$users = User::all ();
			$roles = role::all ();
			return view ('admin.users', compact('users', 'roles'));
		}
		else
		{
			return redirect ('home');
		}
    }


    /**
     * 		dodawanie użytkownika jest w metodzie "edit"
     *
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * 		Dodawanie lub edycja użytkownika - w zależności od obecności parametru id w formularzu
     *
     */
    public function edit(Request $request)
    {
		if ($this -> has_permissions())
		{
			$fields = [
				'email' => '"adres e-mail"',
				'telephone' => '"telefon"',
				'name' => '"nazwa użytkownika"',
				'horses_count' => '"liczba koni"',
				'role' => '"rola"'
			];

			$messages = [
				'email.unique' => 'Użytkownik o podanym adresie e-mail już istnieje.',
				'name.unique' => 'Użytkownik o podanej nazwie już istnieje.'
			];
			if (isset($request->id))
			{
				$u = User::find($request->id);

				$validator = Validator::make($request->all(), [
					'id' => 'required|integer',
					"email" => 'required|string|email|max:255|unique:users,email,'.$u->id,
					"name" => 'required|string|max:30|unique:users,name,'.$u->id,
					"telephone" => 'max:13',
					"horses_count" => 'required|integer|min:0',
					"role" => 'required|integer'
				],
				$messages,
				$fields);

				if ($validator->fails()) {
					return redirect()->route('admin_users')->withErrors($validator)->withInput();
				}

				$validated = $validator->validated();


				$u -> email = $request -> email;
				$u -> name = $request -> name;
				if (isset ($request -> telephone))
					$u -> telephone = $request -> telephone;
				$u -> horses_count = $request -> horses_count;

				// jeśli edytujemy siebie
				if ($request -> id == Auth::user() -> id)
				{
					// ale chcemy sobie ustawić rolę bez dostępu do panelu admina
					if (role::find($request -> role) -> can_enter_admin_panel == 0)
						return redirect()->route('admin_users')->withErrors(['role' => 'Nie możesz ustawić sobie roli, która nie ma dostępu do panelu administratora. Może to tylko zrobić ktoś inny.'])->withInput();
					else
						$u -> role_id = $request -> role;
				}
				else
					$u -> role_id = $request -> role;

				$u -> save();

				return redirect()->route('admin_users')->with('message', 'Edycja użytkownika powiodła się.');

			}
			else
			{
				$validator = Validator::make($request->all(), [
					"email" => 'required|string|email|max:255|unique:users,email',
					"name" => 'required|string|max:30|unique:users,name',
					"telephone" => 'max:13',
					"horses_count" => 'required|integer|min:0',
					"role" => 'required|integer'
				],
				$messages,
				$fields);

				if ($validator->fails()) {
					return redirect()->route('admin_users')->withErrors($validator)->withInput();
				}


				$validated = $validator->validated();

				$u = new User();
				$u -> email = $request -> email;
				$u -> name = $request -> name;
				if (isset ($request -> telephone))
					$u -> telephone = $request -> telephone;
				$u -> horses_count = $request -> horses_count;
				$u -> role_id = $request -> role;
				$u -> password = '';

				$token = \Illuminate\Support\Facades\Password::broker('users')->createToken($u);
				$u -> sendWelcomeNotification ($token);
				$u -> save();

				return redirect()->route('admin_users')->with('message', 'Dodano nowego użytkownika i wysłano mu e-mail z zaproszeniem.');

			}
		}
		else
		{
			return redirect ('home');
		}
	}

    /**
     * 		usuń użytkownika
     *
     */
	public function destroy($id)
    {
        //
		if ($this -> has_permissions())
		{
			// nie możemy usunąć siebie
			if ($id == Auth::user() -> id)
			{
				return redirect ()->route('admin_users')->with('message', 'Nie możesz usunąć siebie. Tylko ktoś inny może to zrobić.');
			}
			else
			{
				User::find($id) -> delete();
				return redirect()->route('admin_users');
			}

		}
		else
		{
			return redirect ('home');
		}
    }
}
