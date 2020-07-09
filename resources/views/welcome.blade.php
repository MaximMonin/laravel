@extends('layouts.app')

@section('head')
    <style>
        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
	.col-1-2 {
	  width: 50%;
	  float: left;
	}
	.col-1-3 {
	  width: 33.3333333333%;
	  float: left;
	}
	.col-1-4 {
	  width: 25%;
	  float: left;
	}
	.col-2-3 {
	  width: 66.6666666667%;
	  float: left;
	}
    </style>
@endsection

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <div class="row">
          <img src="/images/milkiland.png"> 
          <div class="title m-b-md">
            {{ config('app.name', 'Laravel') }}
          </div>
        </div>
      </div>
      <div class="col-md-5">
        @guest
	  @include ('auth.login2')
        @else
        <div class="col-md-8">
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
                    <a href="https://sumy.milkiland.org" target="_blank">{{ __('ents.Sumy') }}</a>
                </div>
            </div>
          </div>
        </div>
        <div class="row">
	  <div class="flex-center position-ref">
             <div class="content">
                <div class="links">
                    <a href="https://chernigov.milkiland.org" target="_blank">{{ __('ents.Chernigov') }}</a>
                </div>
            </div>
          </div>
        </div>
        <div class="row">
	  <div class="flex-center position-ref">
             <div class="content">
                <div class="links">
                    <a href="https://ioblik.milkiland.org" target="_blank">{{ __('Oblik Saas Docker') }}</a>
                </div>
            </div>
          </div>
        </div>

  </div>

@endsection
