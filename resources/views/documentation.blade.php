@extends('layouts.app')

@section('content')
<div class="main-content">
  <div class="container">
    <div class="col-md-12">
      <h5 class="title-spravka">{{ $titleFull }} </h5>
    </div>
    <div class="row">
      <div class="col-md-4">
	<div class="sidebar">
 	  <div class="card">
	    <h5 class="card-header">{{ __('messages.Documentation') }}</h5>
	    <div class="card-body">
	      <form id="help_searchbox_form" action="/documentation" method="GET">
		<div class="row-input search-text-input">
	 	  <input id="help_searchbox" type="text" name="searchText" value="{{ $search }}" placeholder="{{ __('messages.Find') }}">
		  <input id="help_searchbox_ok" type="submit" value="{{ __('messages.Search') }}" class="search-submit">
		  <input id="help_searchbox_cancel" type="button" value="{{ __('messages.CancelSearch') }}" class="cancel-search-submit"
                                            onclick="event.preventDefault();
                                                    document.getElementById('help_searchbox').value = '';
                                                    document.getElementById('help_searchbox_form').submit();
                                            ">
		</div>
	      </form>
	    </div>
	    <div class="main-menu-help">
		<ul class="list_help">
                  {!! $tree !!}
		</ul>
	    </div>
	  </div>
	</div>
      </div>
      <div class="col-md-8">
	<div class="content">
  	  <h3 class="title-page title-spravka-main">{{ $title }}</h3>
 	  <div id="help_page_text_container" style="clear: both;">
 	    {!! $text !!}
	  </div>
	  <div class="pagination-help">
	    <div class="paginationlinks">
             @if ($prevUrl !== '')
                   <a href={{ $prevUrl }} class="help-link prev-link">&lt;&lt; Предыдущая</a>
             @endif
             @if ($nextUrl !== '')
               <a href={{ $nextUrl }} class="help-link next-link">Следующая &gt;&gt;</a>
             @endif
	    </div>
	  </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
