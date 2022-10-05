@extends('layouts.app')

@section('title', 'Ustawienia ogólne')

@section('content')

<div class="container">

	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header d-flex flex-row justify-content-between">
					<strong>Ustawienia ogólne serwisu</strong>
				</div>

				<div class="card-body">
					<form method="POST" action="{{ route('admin_settings.edit') }}" id="edit_form">
						@csrf
						<div class="form-group row mt-2" id="af_errors">
							@if ($errors->any())
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
							<label for="opening_hour" class="col-md-4 col-form-label text-md-right">Godzina otwarcia</label>
							<div class="col-md-6">
								<input id="opening_hour" name="opening_hour" type="number" class="form-control @error('opening_hour') is-invalid @enderror" min="1" max="23" value="{{ $opening_hour }}" required>
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="closing_hour" class="col-md-4 col-form-label text-md-right">Godzina zamknięcia</label>
							<div class="col-md-6">
								<input id="closing_hour" name="closing_hour" type="number" class="form-control @error('closing_hour') is-invalid @enderror" min="1" max="23" value="{{ $closing_hour }}" required>
							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="allow_changing_username" class="col-md-4 col-form-label text-md-right">Pozwól użytkownikom na zmianę swojej nazwy</label>
							<div class="col-md-6 pt-3">
								<input type="checkbox" name="allow_changing_username" id="allow_changing_username" {{ $allow_changing_username == 1 ? 'checked="checked"' : '' }}>

							</div>
						</div>

						<div class="form-group row mt-2">
							<label for="contact_info">Zawartość podstrony "Kontakt":</label>

							<textarea id="contact_info" name="contact_info" class="form-control" placeholder="dane kontaktowe" style="text-align:center; height: 200px">{{ $contact_info }}</textarea>
						</div>

						<div style="float:right">
							<input type="submit" class="btn btn-primary row mt-2" value="Zapisz">
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
