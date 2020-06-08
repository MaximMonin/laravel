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
 
	#my-dropzone .message {
	    font-family: "Segoe UI Light", "Arial", serif;
	    font-weight: 600;
	    color: #0087F7;
	    font-size: 1.5em;
	    letter-spacing: 0.05em;
	}
 
	.dropzone {
	    border: 2px dashed #0087F7;
	    background: white;
	    border-radius: 5px;
	    min-height: 300px;
	    padding: 90px 0;
	    vertical-align: baseline;
	}
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <h2 class="page-heading">Upload your Images <span id="counter"></span></h2>
            <form method="post" action="{{ url('upload') }}"
                  enctype="multipart/form-data" class="dropzone" id="documentDropzone">
                {{ csrf_field() }}
                <div class="dz-message">
                    <div class="col-xs-8">
                        <div class="message">
                            <p>Drop files here or Click to Upload</p>
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
  @if( Request::server('HTTP_X_FORWARDED_PROTO') == 'https' )
    url: '{{ secure_url('/upload') }}',
  @else
    url: '{{ url('/upload') }}',
  @endif
    maxFilesize: 500, // MB
    chunking: true,
    chunkSize: 2000000, 
    parallelChunkUploads: true,
    retryChunks: true,
    retryChunksLimit: 3,
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
      uploadedDocumentMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDocumentMap[file.name]
      }
      $('form').find('input[name="document[]"][value="' + name + '"]').remove()
    },
    init: function () {
      @if(isset($project) && $project->document)
        var files =
          {!! json_encode($project->document) !!}
        for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
        }
      @endif
    }
  }
</script>
@endsection
