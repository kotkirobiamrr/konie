@extends('layouts.app')

@section('title', 'Rezerwacje')

@section('content')

<div class="container">
<!-- Button trigger modal -->

  <!-- okienko ze szczegółami danego odcinka czasu -->
	<div class="modal fade" id="details-modal" tabindex="-1" aria-labelledby="details-modal-title" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="details-modal-title">xD</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
				</div>
				<div class="modal-body" >
					<table class="table text-center">
						<thead>
							<tr>
								<th scope="col">Kto</th>
								<th scope="col">Ile koni</th>
								<th scope="col">Od</th>
								<th scope="col">Do</th>
								<th scope="col">Komentarz</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody id="details-modal-tbody">
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>

	  <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card">

				<div class="card-header">

					<ul class="pagination justify-content-center mb-0">
						<li class="page-item">
							<a class="page-link" href="#" role="button" onclick="incrementDate(-1)">&laquo;</a>
						</li>

						<li class="page-item flex-grow-1">
							<label class="page-link text-center"  for="select-date">
								<span id="reservations-header"></span>
								<input type="date" name="select-date" id="select-date" onchange="setDate(this)">
							</label>

						</li>
						<li class="page-item">
							<a class="page-link" href="#" role="button" onclick="incrementDate(1)">&raquo;</a>
						</li>
					</ul>
				</div>

					@if ($user -> role -> can_add_reservation == 1)
						<div class="card mb-3 d-none" id="reservation-form">
							<div class="card-header">
								Dodaj rezerwację
							</div>

							<form class="row g-3 card-body" id="add-reservation-form" action="{{ route('reservations.add')}}" method="post">
								@csrf
								<input type="hidden" name="f_date" id="f_date" value="{{ old('f_date') }}">
								<div id="st-hr-too-late-alert" class="alert alert-danger mb-0">
									Godzina rozpoczęcia rezerwacji jest taka sama lub późniejsza niż zakończenia.
								</div>
								<div id="st-hr-in-past" class="alert alert-danger mb-0">
									Godzina rozpoczęcia rezerwacji znajduje się w przeszłości.
								</div>
								<div id="res-too-long" class="alert alert-danger mb-0">
									Rezerwacja trwa za długo (max. {{ $user -> role -> max_reservation_length }} minut).
								</div>
								<div id="excl-res-viol-alert" name="limits" class="alert alert-danger mb-0">
									Naruszasz czyjąś rezerwację na wyłączność.
								</div>
								<div id="excd-horse-limit-alert" name="limits" class="alert alert-danger mb-0">
									Przekraczasz dopuszczalną liczbę koni w danym miejscu.
								</div>
								<div id="excd-my-horse-limit-alert" name="limits" class="alert alert-danger mb-0">
									Przekraczasz swoją liczbę koni w jednym czasie.
								</div>
								<div class="col">
									<label for="f_st_hour" class="form-label">Godzina rozpoczęcia</label>
									<div class="row g-1">
										<select class="form-select col" name="f_st_hour" id="f_st_hour" onchange="checkReservationForm()">
											@for ($i = $opening_hour; $i < $closing_hour; $i++)
												@if(old('f_st_hour', null) == $i)
													<option selected value="{{ $i }}">{{ $i }}</option>
												@else
													<option value="{{ $i }}">{{ $i }}</option>
												@endif
											@endfor
										</select>
										<select class="form-select col" name="f_st_minute" id="f_st_minute" onchange="checkReservationForm()">
											@if(old('f_st_minute', null) == 30)
												<option value="00">00</option>
												<option selected value="30">30</option>
											@else
												<option value="00">00</option>
												<option value="30">30</option>
											@endif
										</select>
									</div>

									<label for="f_area" class="form-label pt-3">Miejsce</label>
									<select class="form-select" id="f_area" name="f_area" onchange="checkReservationForm()">
										@foreach ($areas as $area)
											@if(old('f_area', null) == $area -> id)
												<option selected value="{{ $area -> id }}">{{ $area -> name }}</option>
											@else
												<option value="{{ $area -> id }}">{{ $area -> name }}</option>
											@endif
										@endforeach
									</select>

									<label for="f_comment" class="form-label pt-3" onchange="showCommentCheckboxChange()">Komentarz</label>
									<input type="text" value="{{ old ('f_comment') }}" maxlength="30" class="form-control" id="f_comment" name="f_comment" placeholder="(opcjonalny, max. 30 znaków)">

								</div>

								<div class="col">
									<label for="f_en_hour" class="form-label">Godzina zakończenia</label>
									<div class="row g-1">
										<select class="form-select col" name="f_en_hour" id="f_en_hour" onchange="checkReservationForm()">
											@for ($i = $opening_hour; $i <= $closing_hour; $i++)
												@if(old('f_en_hour', null) == $i)
													<option selected value="{{ $i }}">{{ $i }}</option>
												@else
													<option value="{{ $i }}">{{ $i }}</option>
												@endif
											@endfor
										</select>
										<select class="form-select col" name="f_en_minute" id="f_en_minute" onchange="checkReservationForm()">
											@if(old('f_en_minute', null) == 30)
												<option value="00">00</option>
												<option selected value="30">30</option>
											@else
												<option value="00">00</option>
												<option value="30">30</option>
											@endif
										</select>
									</div>

									<label for="f_horse_count" class="form-label pt-3">Liczba koni</label>
									<input type="number" onchange="checkReservationForm()"
										@if ($user -> role -> can_force_limit == 1)
											min="0"
										@else
											min="1" max="{{ $user -> horses_count }}"
										@endif
											value="{{ old('f_horse_count', 1) }}"
											class="form-control" id="f_horse_count" name="f_horse_count" required>


									@if ($user -> role -> can_force_limit == 1 || $user -> role -> can_reserve_exclusively == 1)
										<label for="f_horse_count" class="form-label pt-3">Opcje</label>

										@if ($user -> role -> can_force_limit == 1)
											<div class="form-check form-switch">
												<input class="form-check-input" type="checkbox" name="f_force_limits" id="f_force_limits" onchange="checkReservationForm()">
												<label class="form-check-label" for="f_force-limits">Pozwól ominąć limity/wyłączność</label>
											</div>
										@endif
										@if ($user -> role -> can_reserve_exclusively == 1)
											<div class="form-check form-switch">
												<input class="form-check-input" type="checkbox" name="f_exclusive_reservation"  id="f_exclusive_reservation" onchange="exclusiveReservationCheckboxChange(this)">
												<label class="form-check-label" for="f_exclusive-reservation">Rezerwacja na wyłączność</label>
											</div>
										@endif
									@endif


								</div>
								<input id="f-submit" type="submit" value="Zatwierdź" class="btn-success btn mt-4">
							</form>
						</div>
					@endif

						<div id="schedule">

						<table id="sched-table" class="table table-sm text-center table-hover">
							<thead>
							</thead>

							<tbody>
							</tbody>
						</table>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
@if(session()->has('message'))
	<script>
		alert ("{{ session()->get('message') }}");
	</script>
@endif

<script src="{{ asset('js/home.js') }}" defer></script>

@if ($errors->any())
<script>
		@foreach ($errors->all() as $error)
			alert ('{{ $error }}');
		@endforeach
		var form_errors = true;
</script>
@endif
@endsection
