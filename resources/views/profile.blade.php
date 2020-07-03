@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/dropzone.css') }}">
    <style>
	#documentDropzone .message {
	    font-family: "Segoe UI Light", "Arial", serif;
	    font-weight: 600;
	    color: #0087F7;
	    font-size: 1em;
	    letter-spacing: 0.05em;
	}
 
	.dropzone {
	    border: 2px dashed #0087F7;
	    background: white;
	    border-radius: 5px;
	    min-height: 150px;
            margin-left: 20px;
	}
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('messages.Profile') }}</div>

                <div class="card-body">
		@if (session('status'))
		    <div class="alert alert-success">
		        {{ session('status') }}
		    </div>
		@endif
                    <form id="sendsms"   action="{{ route('sendsms') }}"     method="POST" style="display: none;">
                            @csrf
                            <div class="col-md-6">
                                <input id="smsphone" type="hidden" name="smsphone" value="{{ $phone }}">
                            </div>
                    </form>
                    <form id="verifysms" action="{{ route('phoneverify') }}" method="POST" style="display: none;">
                            @csrf
                            <div class="col-md-6">
                                <input id="smscode" type="hidden" name="smscode" value="">
                            </div>
                    </form>

                    <form method="POST" action="{{ route('saveprofile') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-6">
                                <input id="id" type="hidden" name="id" value="{{ $id }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="avatar" class="col-md-4 col-form-label text-md-right">{{ __('upload.Avatar') }}</label>
                            <input id="avatar" type="hidden" name="avatar" value="{{ $avatar }}">
                            <img class="centered-and-cropped" id="avatarimg" width="150" height="150" style="border-radius:50%" src="{{ $avatar }}"> 
                            <div enctype="multipart/form-data" action='{{ url("/upload/local") }}' class="dropzone" id="avatarDropzone">
                              <div class="dz-message">
                                 <div class="col-xs-8">
                                   <div class="message">
                                      <p>{{ __('upload.ChangeAvatar') }}</p>
                                   </div>
                                 </div>
                              </div>
                           </div>

		        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $name }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email }}" readonly="true">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @if ($email_verified_at !== null)
                                  <span class="text-success">
                                      <strong>{{__('messages.Verified')}}</strong>
                                  </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('messages.PhoneNumber') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ $phone }}" placeholder="380505005050" required autocomplete="phone">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                @if ($phone_verified_at !== null)
                                  <span class="text-success">
                                      <strong>{{__('messages.Verified')}}</strong>
                                  </span>
                                @endif
                                <div id="otpSection" class="otpSection">
                                @if (!session('status') and $phone_verified_at === null)
			            <div id="sendOtpSection" class="sendOtpSection" style="display:block">
			                <button class="btn" id="BntSendsms" 
                                            onclick="event.preventDefault(); 
                                                     document.getElementById('smsphone').value = document.getElementById('phone').value;
                                                     document.getElementById('sendsms').submit();
                                                    ">
        	                           {{__('messages.Submit')}}
                	                </button>
			            </div>
                                @endif
			        </div>
                            </div>
                        </div>
                        @if (session('status') and $phone_verified_at === null)
                        <div class="form-group row">
                            <label for="otp" class="col-md-4 col-form-label text-md-right">{{__('messages.EnterSmsCode')}}</label>
                            <div class="col-md-4">
  	                      <input type="number" class="form-control" id="otp" placeholder="000000" autocomplete="otp">
                            </div>
		            <button class="btn" id="BntVerifysms" 
                                            onclick="event.preventDefault();
                                                    document.getElementById('smscode').value = document.getElementById('otp').value;
                                                    document.getElementById('verifysms').submit();
                                            ">
        	                           {{__('messages.Send')}}
                            </button>
                        </div>
                        @endif

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('messages.Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script src="{{ asset('js/dropzone.js') }}"></script>
<script>
  var uploadedDocumentMap = {}
  Dropzone.options.avatarDropzone = {
    url: '{{ url("/upload/local?filedir=cdn/avatar&action=avatar") }}',
    parallelUploads: 1,
    maxFilesize: 2, // MB
    maxFiles: 1, 
    chunking: false,
    addRemoveLinks: false,
    acceptedFiles: "image/*",
    dictInvalidFileType: '{{ __("upload.InvalidFileType") }}',
    dictFileTooBig: '{{ __("upload.FileTooBig") }}',
    dictResponseError: '{{ __("upload.error") }}',
    dictCancelUpload: '{{ __("upload.cancel") }}',
    dictCancelUploadConfirmation: '{{ __("upload.cancelConfirmation") }}',
    dictUploadCanceled: '{{ __("upload.canceled") }}',
    dictRemoveFile: '{{ __("upload.remove") }}',
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    success: function (file, response) {
      if (response.name) {
        document.getElementById('avatarimg').src = "cdn/avatar/" + response.name;
        document.getElementById('avatar').value = "cdn/avatar/" + response.name;      
      }
    },
    init: function() {
      this.on('addedfile', function(file) {
        if (this.files.length > 1) {
          this.removeFile(this.files[0]);
        }
     });
    } 
  }
</script>
@endsection

