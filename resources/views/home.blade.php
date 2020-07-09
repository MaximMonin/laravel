@extends('layouts.app')

@section('content')
<div class="container">
   <div class="col-md-12">
     <h5 class="title-spravka">{{ __('messages.Applications') }}</h5>
   </div>
   <div class="container">
       <div class="links">
          <a href="https://sumy.milkiland.org" target="_blank">{{ __('ents.Sumy') }}</a>
       </div>
       <div class="links">
          <a href="https://chernigov.milkiland.org" target="_blank">{{ __('ents.Chernigov') }}</a>
       </div>
       <div class="links">
          <a href="https://ioblik.milkiland.org" target="_blank">{{ __('Oblik Saas Docker') }}</a>
       </div>
   </div>
</div>
@endsection
