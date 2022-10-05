@extends('layouts.app')

@section('title', 'Użytkownicy')

@section('content')

<div class="container">
  <!-- okienko ze szczegółami -->
	<div class="modal fade" id="admin-details-modal" tabindex="-1" aria-labelledby="admin-details-modal-title" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="admin-details-modal-title">Dodawanie/edycja użytkownika</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
				</div>
				<div class="modal-body" >
					<form method="POST" action="{{ route('admin_users.edit') }}" id="edit_form">
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
							<label for="email" class="col-md-4 col-form-label text-md-right">e-mail</label>
							<div class="col-md-6">
								<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required >
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="name" class="col-md-4 col-form-label text-md-right">Nazwa użytkownika</label>
							<div class="col-md-6">
								<input id="name" type="text" maxlength="30" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required >
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="telephone" class="col-md-4 col-form-label text-md-right">Numer telefonu</label>
							<div class="col-md-6">
								<input id="telephone" type="text" maxlength="13" class="form-control @error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone') }}" >
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="horses_count" class="col-md-4 col-form-label text-md-right">Liczba koni</label>
							<div class="col-md-6">
								<input id="horses_count" type="number" min="0" class="form-control @error('horses_count') is-invalid @enderror" name="horses_count" value="{{ old('horses_count', 1) }}" required>
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="email" class="col-md-4 col-form-label text-md-right">Rola</label>
							<div class="col-md-6">
								<select id="role" class="form-select @error('role') is-invalid @enderror" name="role" required >
									@foreach ($roles as $r)
										@if(old('role', null) == $r -> id)
											<option selected value="{{ $r -> id }}">{{ $r -> name }}</option>
										@else
											<option value="{{ $r -> id }}">{{ $r -> name }}</option>
										@endif
									@endforeach
								</select>
							</div>
						</div>

						<div id="edit_options" class="d-none">
							<div class="form-group row mt-2">
								<button type="button" onclick="deleteUser('{{ route('admin_users.delete_view') }}')" class="btn btn-danger col-md-4">Usuń użytkownika</button>
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
					<strong>Zarządzanie użytkownikami</strong>
					<button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#admin-details-modal" onclick="showUserEditForm()">Dodaj użytkownika</button>
				</div>




				<table class="table text-center table-hover">
					<thead>
						<tr>
							<th>id</th>
							<th>e-mail</th>
							<th>nazwa</th>
							<th>nr tel.</th>
							<th>liczba koni</th>
							<th>rola</th>
							<th>data rejestracji</th>
							<th></th>
						</tr>
					</thead>

					<tbody>
						@foreach ($users as $u)
							<tr>
								<td class="af_id">{{ $u -> id }}</td>
								<td class="af_email">{{ $u -> email }}</td>
								<td class="af_name">{{ $u -> name }}</td>
								<td class="af_telephone">{{ $u -> telephone }}</td>
								<td class="af_horses_count">{{ $u -> horses_count }}</td>
								<td class="af_role" style="color: {{ $u -> role -> color }}">{{ $u -> role -> name }}</td>
								<td>{{ $u -> created_at }}</td>
								<td>
									<button data-bs-toggle="modal" data-bs-target="#admin-details-modal" class="btn btn-sm btn-primary" onclick="showUserEditForm(this.parentNode.parentNode)">
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
