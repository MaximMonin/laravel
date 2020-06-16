@extends('layouts.app')

@section('head')
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <h2 class="page-heading">{{ __('upload.title') }}<span id="counter"></span></h2>
            <form method="post" action="{{ url('upload') }}"
                  enctype="multipart/form-data" class="dropzone" id="documentDropzone">
                {{ csrf_field() }}
                <div class="dz-message">
                    <div class="col-xs-8">
                        <div class="message">
                            <p>{{ __('upload.message') }}</p>
                        </div>
                    </div>
                </div>
                <div class="fallback">
                    <input type="file" name="file" multiple>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
<script>
Echo.channel('chat.0')
	  .listen('ChatMessage', (msg) => {
			alert(msg.chatMessage);
	  })
		
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
