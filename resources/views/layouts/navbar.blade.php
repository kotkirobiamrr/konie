
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
	<div class="container">
		<a class="navbar-brand" href="{{ url('/') }}">
			{{ config('app.name') }}
		</a>

	@auth
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar_hidden">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbar_hidden">
			<div class="navbar-nav me-auto mb-2 mb-lg-0">
			@isset($upcoming_timestamp)
				<div class="navbar-text" id="upcoming-reservation" data-timestamp="{{ $upcoming_timestamp }}" >
				</div>
			@endisset
			</div>


			<div class="d-flex navbar-nav">



				<div class="nav-item dropdown">
					<a id="navbarDropdown" class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" v-pre>
						<span class="small" style="color:{{ Auth::user() -> role -> color }}">
							{{ Auth::user() -> role -> name }}
						</span>
						{{ Auth::user() -> name }}
					</a>
					<ul class="dropdown-menu dropdown-menu-end">
						<li>
							<a class="dropdown-item" href="{{ route('contact') }}">
								Kontakt z ośrodkiem
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ route('accountsettings') }}">
								Ustawienia konta
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ route('logout') }}"
								onclick="event.preventDefault();
								document.getElementById('logout-form').submit();">
								Wyloguj mnie
							</a>
						</li>

						<!-- opcje administratora -->
						@if (Auth::user() -> role -> can_enter_admin_panel)
							<li>
								<h6 class="dropdown-header">Opcje administracyjne</h6>
							</li>
							<li>
								<a class="dropdown-item" href="{{ route('admin_settings') }}">
									Ustawienia ogólne
								</a>
							</li>
							<li>
								<a class="dropdown-item" href="{{ route('admin_users') }}">
									Zarządzanie użytkownikami
								</a>
							</li>
							<li>
								<a class="dropdown-item" href="{{ route('admin_areas') }}">
									Zarządzanie miejscami treningowymi
								</a>
							</li>
						@endif

						<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
							@csrf
						</form>

					</ul>
				</div>
			</div>
	 	</div>
	</div>
	@endauth
	@guest
	<div class="nav-item">
		<a class="nav-link active" href="{{ route('contact') }}">Kontakt</a>
	</div>
	@endguest
</nav>
