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
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <h2 class="page-heading">{{ __('upload.title') }}<span id="counter"></span></h2>
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
@endsection

@section('js')
    <script src="{{ asset('js/dropzone.js') }}"></script>
<script>
  var uploadedDocumentMap = {}
  Dropzone.options.documentDropzone = {
    url: '{{ url("/upload/$storage") . "?filedir=$filedir&action=$action" }}',
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
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      name = uploadedDocumentMap[file.name]
      axios.post('{{ url("/upload/$storage/delete") }}', { 'file': name}).then(response => {});
    },
    init: function () {
    }
  }
</script>
@endsection
