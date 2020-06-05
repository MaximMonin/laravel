@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('messages.Contacts') }}</div>

                <div class="card-body">
                @if (App::isLocale('en'))
			@include ('en.contact')
                @endif
                @if (App::isLocale('ru'))
			@include ('ru.contact')
                @endif
                @if (App::isLocale('uk'))
			@include ('uk.contact')
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
