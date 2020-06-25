@extends('layouts.app')

@section('head')
<style>
        .panel-heading {
           height: 20px
        }
        .panel-footer {
           padding-top: 10px;
           height: 50px
        }
        .chat-client-body {
           height: calc(100vh - 170px);
           position: relative;
        }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Chats</div>

                <div class="chat-client-body border">
                  <chat-messages :messages="{{$messages}}"></chat-messages>
                </div>
                <div class="panel-footer">
                    <chat-form
                        :user="{{ Auth::user() }}"
                    ></chat-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection
