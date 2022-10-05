@extends('layouts.app')

@section('title', 'Ustawienia konta')

@section('content')

<div class="container">

	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header d-flex flex-row justify-content-between">
					<strong>Ustawienia ogólne konta</strong>
				</div>

				<div class="card-body">
					<form method="POST" action="{{ route('accountsettings.general') }}">
						@csrf
						<div class="form-group row mt-2">
							@error ('name')
								<div class="alert alert-danger">
									{{ $message }}
								</div>
							@enderror
							@error ('telephone')
								<div class="alert alert-danger">
									{{ $message }}
								</div>
							@enderror
						</div>

						<div class="form-group row mt-2">
							<label for="name" class="col-md-4 col-form-label text-md-right">Nazwa użytkownika</label>
							<div class="col-md-6">
								<input id="name" name="name" type="text" maxlength="30" class="form-control @error('name') is-invalid @enderror" value="{{ old ('name', $user -> name) }}" required {{ $can_change_name == 0 ? 'disabled' : '' }}>
							</div>
						</div>
						<div class="form-group row mt-2">
							<label for="role" class="col-md-4 col-form-label text-md-right">Rola</label>
							<div class="col-md-6">
								<input id="role" name="role" type="text" class="form-control" value="{{ $user -> role -> name }}" disabled>
							</div>
						</div>
						<div class="form-group row mt-2">
							<label for="role" class="col-md-4 col-form-label text-md-right">Liczba koni</label>
							<div class="col-md-6">
								<input id="horses_count" name="horses_count" type="number" class="form-control" value="{{ $user -> horses_count }}" disabled>
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="telephone" class="col-md-4 col-form-label text-md-right">Numer telefonu</label>
							<div class="col-md-6">
								<input id="telephone" name="telephone" type="text" maxlength="13" class="form-control @error('telephone') is-invalid @enderror" value="{{ old ('telephone', $user -> telephone) }}">
							</div>
						</div>

						<div class="form-group row mb-0 mt-2">
							<div class="col-md-8 offset-md-4">
								<input type="submit" class="btn btn-primary" value="Zapisz">
							</div>
						</div>

					</form>
				</div>
            </div>
        </div>

		<div class="col-md-8 mt-2">
			<div class="card">
				<div class="card-header d-flex flex-row justify-content-between">
					<strong>Zmiana adresu e-mail</strong>
				</div>

				<div class="card-body">
					<form method="POST" action="{{ route('accountsettings.email') }}">
						@csrf
						<div class="form-group row mt-2">
							@error ('email')
								<div class="alert alert-danger">
									{{ $message }}
								</div>
							@enderror
							@error ('email_password')
								<div class="alert alert-danger">
									{{ $message }}
								</div>
							@enderror
						</div>

						<div class="form-group row mt-2">
							<label for="email" class="col-md-4 col-form-label text-md-right">Adres e-mail</label>
							<div class="col-md-6">
								<input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old ('email', $user -> email) }}" required>
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="email_password" class="col-md-4 col-form-label text-md-right">Hasło</label>
							<div class="col-md-6">
								<input id="email_password" name="email_password" type="password" class="form-control @error('email_password') is-invalid @enderror" placeholder="wpisz hasło dla bezpieczeństwa" required>
							</div>
						</div>

						<div class="form-group row mb-0 mt-2">
							<div class="col-md-8 offset-md-4">
								<input type="submit" class="btn btn-primary" value="Zapisz">
							</div>
						</div>

					</form>
				</div>
            </div>
        </div>

		<div class="col-md-8 mt-2">
			<div class="card">
				<div class="card-header d-flex flex-row justify-content-between">
					<strong>Zmiana hasła</strong>
				</div>

				<div class="card-body">
					<form method="POST" action="{{ route('accountsettings.password') }}">
						@csrf
						<div class="form-group row mt-2">
							@error ('current_password')
								<div class="alert alert-danger">
									{{ $message }}
								</div>
							@enderror
								@error ('new_password1')
								<div class="alert alert-danger">
									{{ $message }}
								</div>
							@enderror
							@error ('new_password2')
								<div class="alert alert-danger">
									{{ $message }}
								</div>
							@enderror
						</div>

						<div class="form-group row mt-2">
							<label for="current_password" class="col-md-4 col-form-label text-md-right">Aktualne hasło</label>
							<div class="col-md-6">
								<input id="current_password" name="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" required>
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="new_password1" class="col-md-4 col-form-label text-md-right">Nowe hasło</label>
							<div class="col-md-6">
								<input id="new_password1" name="new_password1" type="password" class="form-control @error('new_password1') is-invalid @enderror" required>
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="new_password2" class="col-md-4 col-form-label text-md-right">Powtórz nowe hasło</label>
							<div class="col-md-6">
								<input id="new_password2" name="new_password2" type="password" class="form-control @error('new_password2') is-invalid @enderror" required>
							</div>
						</div>

						<div class="form-group row mb-0 mt-2">
							<div class="col-md-8 offset-md-4">
								<input type="submit" class="btn btn-primary" value="Zapisz">
							</div>
						</div>

					</form>
				</div>
            </div>
        </div>

		<div class="col-md-8 mt-2">
			<div class="card">
				<div class="card-header d-flex flex-row justify-content-between">
					<strong>Usuń konto</strong>
				</div>

				<div class="card-body">
					<strong>Uwaga! Operacja jest nieodwracalna.</strong>

					<form method="POST" action="{{ route('accountsettings.delete') }}">
						@csrf
						<div class="form-group row mt-2">
							@if ($errors->any())
								@error ('delete_password')
									<div class="alert alert-danger">
										{{ $message }}
									</div>
								@enderror
							@endif
						</div>

						<div class="form-group row mt-2">
							<label for="delete_password" class="col-md-4 col-form-label text-md-right">Hasło</label>
							<div class="col-md-6">
								<input id="delete_password" name="delete_password" type="password" class="form-control @error('delete_password') is-invalid @enderror" placeholder="wpisz hasło dla bezpieczeństwa" required>
							</div>
						</div>

						<div class="form-group row mb-0 mt-2">
							<div class="col-md-8 offset-md-4">
								<input type="submit" class="btn btn-danger" value="Usuń konto">
							</div>
						</div>

					</form>
				</div>
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
