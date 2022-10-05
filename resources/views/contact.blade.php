@extends('layouts.app')

@section('title', config('app.name').' - kontakt')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Kontakt z nami</div>

                <div class="card-body" style="text-align: center">
					{!! nl2br(e($contact)) !!}
				</div>
            </div>
        </div>
    </div>
</div>
@endsection
