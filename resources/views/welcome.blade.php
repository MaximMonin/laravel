@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-sm-7">
        <div class="row">
          <img src="/images/milkiland.png"> 
          <div class="title m-b-md">
            {{ config('app.name', 'Laravel') }}
          </div>
        </div>
      </div>
      <div class="col-sm-5">
        @guest
	  @include ('auth.login2')
        @else
        <div class="col-sm-8">
            <div class="card">
                <div class="card-header">Welcome</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
        @endguest
      </div>
    </div>
        <div class="row">
	  <div class="flex-center position-ref">
             <div class="content">
                <div class="links">
                    <a href="https://sumy.milkiland.org">{{ __('ents.Sumy') }}</a>
                </div>
            </div>
          </div>
        </div>
        <div class="row">
	  <div class="flex-center position-ref">
             <div class="content">
                <div class="links">
                    <a href="https://chernigov.milkiland.org">{{ __('ents.Chernigov') }}</a>
                </div>
            </div>
          </div>
        </div>

  </div>

@endsection
