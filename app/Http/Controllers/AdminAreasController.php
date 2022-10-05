<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class AdminAreasController extends Controller
{
    /**
     * wyświetl place - miejsca
     *
     * @return \Illuminate\Http\Response
     */
	public function index()
    {
        //
		if ($this -> has_permissions())
		{

			// wybieramy miejsca + liczbę niezaczętych rezerwacji
			$areas = Area::withCount(['reservations' => function (Builder $query) {
				$query	->where('start', '>', Carbon::now())
						-> orWhere ('end', '>', Carbon::now());
			}])
				-> orderBy ('display_order')
				-> get();

			return view ('admin.areas', compact('areas'));
		}
		else
		{
			return redirect ('home');
		}
    }

	public function has_permissions()
	{
		return Auth::user() -> role -> can_enter_admin_panel == 1;
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
		if ($this -> has_permissions())
		{
			$fields = [
				'name' => '"nazwa"',
				'horse_limit' => '"limit koni"',
				'display_order' => '"kolejność wyświetlania"'
			];


			$validator = Validator::make($request->all(), [
				'id' => 'nullable|integer',
				"name" => 'required|string|max:255',
				"horse_limit" => 'required|integer|min:0',
				"display_order" => 'required|integer|min:0',
			],
			[],
			$fields);

			if ($validator->fails()) {
				return redirect()->route('admin_areas')->withErrors($validator)->withInput();
			}

			$validated = $validator->validated();

			if (isset($request->id))
				$a = Area::find($request->id);
			else
				$a = new Area();
			$a -> name = $request -> name;
			$a -> horse_limit = $request -> horse_limit;
			$a -> display_order = $request -> display_order;

			$a -> save();

			if (isset($request->id))	//jest id, więc edycja
			{
				return redirect()->route('admin_areas')->with('message', 'Edycja powiodła się.');
			}
			else	//tworzenie nowego
			{
				return redirect()->route('admin_areas')->with('message', 'Dodano miejsce.');
			}
		}
		else
		{
			return redirect ('home');
		}
    }

    /**
     * Usuwanie placu treningowego
     *
     */
	public function destroy($id)
    {
		if ($this -> has_permissions())
		{
			Area::find($id) -> delete();
			return redirect()->route('admin_areas');
		}
		else
		{
			return redirect ('home');
		}
    }
}
