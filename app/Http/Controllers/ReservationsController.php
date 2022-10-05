<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Area;
use App\Models\Schedule;
use App\Models\Reservation;
use App\Models\settings;

// ustalamy podziałkę czasu na 30 minut
const time_scale = 30;

class ReservationsController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * sprawdź i zapisz rezerwację
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		if (Auth::user() -> role -> can_add_reservation)
		{
			$request -> validate ([
				'f_date' => 'required',
				"f_st_hour" => 'required|integer',
				"f_st_minute" => 'required|digits:2',
				"f_area" => 'required|integer',
				"f_comment" => 'max:30',
				"f_en_hour" => 'required|integer',
				"f_en_minute" => 'required|digits:2',
				"f_horse_count" => 'required|integer',
			]);

			// przypisujemy godziny
			$r = new Reservation ();
			$r -> start = new Carbon ($request -> f_date .' '.$request -> f_st_hour .':'. $request -> f_st_minute);
			$r -> end = new Carbon ($request -> f_date .' '.$request -> f_en_hour .':'. $request -> f_en_minute);
			// sprawdzamy, czy początek jest przed końcem
			if ($r -> start >= $r -> end)
			{
				return redirect('home')
					->withErrors([ 1 => 'Godzina rozpoczęcia rezerwacji jest taka sama lub późniejsza niż zakończenia.'])
					->withInput();
			}
			// sprawdzamy, czy początek jest w przyszłości
			if ($r -> start <= Carbon::now())
			{
				return redirect('home')
					->withErrors([ 2 => 'Godzina rozpoczęcia rezerwacji znajduje się w przeszłości. Prawdopodobnie zbyt poźno wysłano formularz.'])
					->withInput();
			}
			// sprawdzamy, czy początek lub koniec nie znajduje się poza godzinami zamknięcia (godzina zamknięcia może być, ale tylko 00)
			$h = settings::find(1);
			if (	$r -> start -> hour < $h -> opening_hour
				||	($r -> end -> hour >= $h -> closing_hour && $r -> end -> minute > 0)
				)
			{

				return redirect('home')
					->withErrors([ 3 => 'Godziny rezerwacji wykraczają poza dozwolony zakres (czynne od '.$h -> opening_hour.' do '.$h -> closing_hour.')'])
					->withInput();
			}
			// sprawdzamy, czy nie przekracza swojego limitu długości rezerwacji, ale tylko, jeśli ma ten limit nałożony (>0)
			$reservation_length = $r->start->diffInMinutes($r -> end);
			if (Auth::user() -> role -> max_reservation_length != 0 &&  $reservation_length > Auth::user() -> role -> max_reservation_length)
			{
				return redirect('home')
				->withErrors([ 4 => 'Rezerwacja trwa za długo (max. '. Auth::user() -> role -> max_reservation_length .' minut).'])
				->withInput();
			}

			// przypisujemy rzeczy dalej
			$r -> area_id = $request -> f_area;
			$r -> user_id = Auth::user() -> id;
			if ($request -> f_comment != null)
				$r -> comment = $request -> f_comment;
			$r -> horses_count = intval($request -> f_horse_count);

			// jeśli zaznaczona opcja rezerwacji na wyłączność  i ma do tego uprawnienia
			if (isset($request -> f_exclusive_reservation) && $request -> f_exclusive_reservation == 'on' && Auth::user() -> role -> can_reserve_exclusively == 1)
			{
				$r -> exclusive = 1;
			}
			// jeśli jest wyłączone przekraczanie limitów
			if (!(isset($request -> f_force_limits) && $request -> f_force_limits == 'on' && Auth::user() -> role -> can_force_limit))
			{
				// sprawdzamy, czy w podanym czasie jest dla niego miejsce

				// robimy to napjprostszym sposobem
				// wybieramy rezerwacje odbywające się w tym czasie i miejscu
				$ts = Reservation::select ('start', 'end', 'horses_count', 'exclusive')
									-> where('start', '<', $r -> end)
									-> where ('end', '>', $r -> start)
									-> where ('area_id', '=', $r -> area_id)
									//-> orderBy ('start')
									-> get();

				// tablica - każde 30 minut to jeden element oznaczający docelową liczbę koni w tym czasie
				$timetable = array_fill (0, ceil ($reservation_length / time_scale), $r -> horses_count);

				// przetwarzamy obecne rezerwacje
				foreach ($ts as $t)
				{

					// jeśli jest to rezerwacja na wyłączność, to przerywamy
					if ($t -> exclusive != 1)
					{
						$tst = Carbon::create ($t -> start);
						$ten = Carbon::create ($t -> end);

						// jeśli czasy początku lub końca wykraczają poza ramy nowej rezerwacji, przycinamy je
						if ($tst < $r -> start)
						{
							$tst = $r -> start;
						}
						if ($ten > $r -> end)
						{
							$ten = $r -> end;
						}

						// obliczamy miejsce początku i końca rezerwacji w naszej tabeli
						$t0 = floor ($r -> start -> diffInMinutes ($tst) / time_scale);
						$tx = ceil ($r -> start -> diffInMinutes ($ten) / time_scale);

						// uzupełniamy tablicę o przetwarzaną rezerwację
						for (; $t0 < $tx; $t0++)
						{
							$timetable[$t0] += $t -> horses_count;
						}
					}
					else
					{
						return redirect('home')
							->withErrors([ 5 => 'Naruszasz czyjąś rezerwację na wyłączność. Prawdopodobnie ktoś dodał taką chwilę przed Tobą.'])
							->withInput();
					}
				}
				// jeśli najwyższa wartość w tablicy jest większa niż dopuszczalna liczba koni
				if (max ($timetable) > $r -> area -> horse_limit)
				{
					return redirect('home')
							->withErrors([ 6 => 'Przekraczasz dopuszczalną liczbę koni w danym miejscu. Prawdopodobnie ktoś dodał rezerwację chwilę przed Tobą.'])
							->withInput();
				}

				////////////////////////////////////////////////////////////
				//
				// sprawdzamy, czy nie przekracza swojego limitu koni - w taki sam sposób


				$ts = Reservation::select ('start', 'end', 'horses_count')
									-> where('start', '<', $r -> end)
									-> where ('end', '>', $r -> start)
									-> where ('user_id', '=', Auth::user() -> id)
									//-> orderBy ('start')
									-> get();

				$timetable = array_fill (0, ceil ($reservation_length / time_scale), $r -> horses_count);

				foreach ($ts as $t)
				{
					$tst = Carbon::create ($t -> start);
					$ten = Carbon::create ($t -> end);

					if ($tst < $r -> start)
					{
						$tst = $r -> start;
					}
					if ($ten > $r -> end)
					{
						$ten = $r -> end;
					}

					$t0 = floor ($r -> start -> diffInMinutes ($tst) / time_scale);
					$tx = ceil ($r -> start -> diffInMinutes ($ten) / time_scale);

					for (; $t0 < $tx; $t0++)
					{
						$timetable[$t0] += $t -> horses_count;
					}
				}
				if (max ($timetable) > Auth::user() -> horses_count)
				{
					return redirect('home')
							->withErrors([ 7  => 'Przekraczasz swoją liczbę koni w jednym czasie.'])
							->withInput();
				}
			}
			//jeśli wszystko poszło pomyślnie
			$r -> save();
			return redirect('home')
					-> withMessage ('Pomyślnie dodano rezerwację.')
					-> withInput(['f_date' => $request -> f_date]);

		}

    }

    /**
     * Zwróć JSON z harmonogramem dla danego dnia
     *
     * @param  string  $date
     * @return \Illuminate\Http\Response
     */
    public function show($date = null)
    {

		if ($date == null)
		{
			$date = Carbon::today();
		}
		else
		{
			$date = Carbon::create ($date);

		}

		$schedule = new Schedule(Auth::user(), $date);

		$areas = Area::orderBy('display_order', 'asc');	//pobierz listę placów

		$schedule ->working_hours = settings::select ('opening_hour', 'closing_hour')-> get()[0];

		$i = 0;
		foreach ($areas -> cursor() as $area)
		{
			$schedule -> schedule [$i] = $area;
			$schedule -> schedule [$i] -> reservations = $area -> get_reservations_on_day ($date);
			$i ++;
		}

		return $schedule -> to_json_string();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
		$r = Reservation::findOrFail ($id);
		$d = Carbon::create($r -> start) -> isoFormat ('YYYY-MM-DD');
		if ($r -> user == Auth::user() || Auth::user() -> role -> can_delete_reservations == 1)
		{
			$r -> delete();
			return redirect('home')
					-> withMessage ('Pomyślnie usunięto rezerwację.')
					-> withInput(['f_date' => $d]);
		}
		else
		{
			return redirect('home')
					-> withMessage ('Nie masz uprawnień do wykonania tej czynności.')
					-> withInput(['f_date' => $d]);
		}

    }
}
