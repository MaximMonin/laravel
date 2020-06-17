@extends('layouts.app')

@section('head')
@endsection

@section('content')
		<div id='app'></div>
		<div class="flex-center position-ref full-height">
			<div class="content">
				<div class="title m-b-md">
					Laravel Event Broadcasting
				</div>
				<div class="links">
					<button onclick="callEvent()" class="btn btn-primary">Call event</button>
				</div>
			</div>
		</div>@endsection

@section('js')
<script>
  Echo.channel('private-chat.0').listen('ChatMessage', (msg) => {
			alert(JSON.stringify(msg));
  });		
  function callEvent(){
	axios.get('/call-event')
		.then((response) => {
			
		})
		.catch(function (error) {
			console.log(error);
		});
  }  
</script>
@endsection
