@extends('layouts.app')

@section('title', 'Miejsca')

@section('content')

<div class="container">
  <!-- okienko ze szczegółami -->
	<div class="modal fade" id="admin-details-modal" tabindex="-1" aria-labelledby="admin-details-modal-title" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="admin-details-modal-title"></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
				</div>
				<div class="modal-body" >
					<form method="POST" action="{{ route('admin_areas.edit') }}" id="edit_form">
						@csrf
						<input id="id" type="hidden" class="form-control @error('id') is-invalid @enderror" name="id" value="{{ old('id') }}">

						<div class="form-group row mt-2" id="af_errors">
							@if ($errors->any())
								<script>
									(new bootstrap.Modal(document.getElementById('admin-details-modal'))).show();
								</script>
								<div class="alert alert-danger">
									<ul class="mb-0">
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							@endif
						</div>

						<div class="form-group row mt-2">
							<label for="name" class="col-md-4 col-form-label text-md-right">Nazwa</label>
							<div class="col-md-6">
								<input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required >
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="horse_limit" class="col-md-4 col-form-label text-md-right">Limit koni w jednym czasie</label>
							<div class="col-md-6">
								<input id="horse_limit" type="number" min="0" class="form-control @error('horse_limit') is-invalid @enderror" name="horse_limit" value="{{ old('horse_limit', 1) }}" required>
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="display_order" class="col-md-4 col-form-label text-md-right">Kolejność wyświetlania</label>
							<div class="col-md-6">
								<input id="display_order" type="number" min="0" class="form-control @error('display_order') is-invalid @enderror" name="display_order" value="{{ old('display_order', 1) }}" required>
							</div>
						</div>

						<div id="edit_options" class="d-none">
							<div class="form-group row mt-2">
								<button type="button" onclick="deleteArea('{{ route('admin_areas.delete_view') }}')" class="btn btn-danger col-md-4">Usuń</button>
							</div>
						</div>


					</form>
				</div>
				<div class="modal-footer">
					<input form="edit_form" type="reset" class="btn btn-secondary" data-bs-dismiss="modal" value="Anuluj">
					<input form="edit_form" type="submit" class="btn btn-primary" value="Zapisz">
				</div>
			</div>
		</div>
	</div>

	<div class="row justify-content-center">

		<div class="">

			<div class="card">

				<div class="card-header d-flex flex-row justify-content-between">
					<strong>Zarządzanie miejscami treningowymi</strong>
					<button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#admin-details-modal" onclick="showAreaEditForm()">Dodaj miejsce</button>
				</div>




				<table class="table text-center table-hover">
					<thead>
						<tr>
							<th>id</th>
							<th>nazwa</th>
							<th>limit koni</th>
							<th>liczba akt. rezerwacji</th>
							<th>kolejność wyświetlania</th>
							<th></th>
						</tr>
					</thead>

					<tbody>
						@foreach ($areas as $a)
							<tr>
								<td class="af_id">{{ $a -> id }}</td>
								<td class="af_name">{{ $a -> name }}</td>
								<td class="af_horse_limit">{{ $a -> horse_limit }}</td>
								<td>{{ $a -> reservations_count }}</td>
								<td class="af_display_order">{{ $a -> display_order }}</td>
								<td>
									<button data-bs-toggle="modal" data-bs-target="#admin-details-modal" class="btn btn-sm btn-primary" onclick="showAreaEditForm(this.parentNode.parentNode)">
										Edytuj
									</button>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/admin.js') }}" defer></script>

@if(session()->has('message'))
	<script>
		alert ("{{ session()->get('message') }}");
	</script>
@endif

@endsection
