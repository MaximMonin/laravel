@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/dropzone.css') }}">
    <style>
	page-heading {
	    margin: 20px 0;
	    color: #666;
	    -webkit-font-smoothing: antialiased;
	    font-family: "Segoe UI Light", "Arial", serif;
	    font-weight: 600;
	    letter-spacing: 0.05em;
	}
 
	#documentDropzone .message {
	    font-family: "Segoe UI Light", "Arial", serif;
	    font-weight: 600;
	    color: #0087F7;
	    font-size: 1.5em;
	    letter-spacing: 0.05em;
	}
 
	.dropzone {
	    border: 2px dashed #0087F7;
	    background: white;
	    border-radius: 15px;
	    min-height: 300px;
	    padding: 90px 0;
	    vertical-align: baseline;
	}
	.myfiles {
	    padding: 10px;
	}
	.myfiles2 {
	    padding-top: 10px;
	}
	.btnupdate {
	    padding-left: 30px;
	}
	.filetop {
	    padding-bottom: 10px;
	}
    </style>
@endsection

@section('content')
<div class="container">
   <div class="col-md-12">
     <div class="row filetop">
       <h4>{{ __('messages.Files') }}</h4>
       <div class="btnupdate">
           <button type="button" class="btn btn-primary btn-sm" onclick="event.preventDefault(); Event.emit('newfileadded', {});">{{ __('messages.Update') }}</button>
       </div>  
       <div class="form-check ml-auto">
           <input type="checkbox" class="form-check-input" id="EditMode" onclick="if (this.checked) {Event.emit('fileseditmode', {})} else {Event.emit('filesviewmode', {})};">
           <label class="form-check-label" for="EditMode">{{ __('messages.EditMode') }}</label>
       </div>  
     </div>
   </div>
   <ul class="nav nav-tabs">
     <li class="nav-item">
       <a class="nav-link active" dusk="photo-tab" data-toggle="tab" href="#photo">{{ __('messages.Photos') }}</a>
     </li>
     <li class="nav-item">
       <a class="nav-link" dusk="video-tab" data-toggle="tab" href="#video">{{ __('messages.Videos') }}</a>
     </li>
     <li class="nav-item">
       <a class="nav-link" dusk="doc-tab" data-toggle="tab" href="#docs">{{ __('messages.Docs') }}</a>
     </li>
     <li class="nav-item">
       <a class="nav-link" dusk="upload-tab" data-toggle="tab" href="#upload" onclick="Dropzone.forElement('#documentDropzone').files.forEach(function(file) { 
                                                                     file.previewElement.remove(); });">{{ __('messages.UploadNew') }}</a>
     </li>
   </ul>
   <div class="tab-content">
     <div class="tab-pane fade show active" id="photo">
       <div class="myfiles2">
         <my-photo></my-photo>
       </div>
     </div>
     <div class="tab-pane fade" id="video">
       <div class="myfiles2">
         <my-video></my-video>
       </div>
     </div>
     <div class="tab-pane fade" id="docs">
       <div class="myfiles2">
         <my-docs></my-doc>
       </div>
     </div>
     <div class="tab-pane fade" id="upload">
       <div class="myfiles">
         <h5 class="page-heading">{{ __('upload.title') }}</h5>
         <form method="post" action='{{ url("/upload/$storage") }}'
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
   </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/dropzone.js') }}"></script>
<script>
  var uploadedDocumentMap = {}
  Dropzone.options.documentDropzone = {
    url: '{{ url("/upload/$storage") . "?filedir=$filedir" }}' + '&' + '{{ "action=$action" }}',
    parallelUploads: 3,
    maxFilesize: 500, // MB
    chunking: true,
    chunkSize: 1000000, // Bytes 
    parallelChunkUploads: false, // true,
    retryChunks: true,
    retryChunksLimit: 3,
    addRemoveLinks: true,
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
        uploadedDocumentMap[file.name] = response.path + '/' + response.name
      }
      Event.emit('newfileadded', {});
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      name = uploadedDocumentMap[file.name]
      axios.post('{{ url("/upload/$storage/delete") }}', { 'file': name}).then(response => {});
      Event.emit('newfileadded', {});
    },
    init: function () {
    }
  }
</script>
@endsection
