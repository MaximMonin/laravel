@extends('layouts.app')

@section('head')
<style>
        .chat {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .chat li {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dotted #B3A9A9;
        }
        .chat li .chat-body p {
            margin: 0;
            color: #777777;
        }
        .chat-client-body {
           height: 750px;
           position: relative;
        }

        .chat-client-conversation {
           padding: 0 12px;
           overflow-y: auto;
           overflow-x: hidden;
           position: absolute;
           bottom: 0; left: 0; right: 0;
           max-height: 100%;
        }
        .panel-footer {
           padding-top: 20px;
        }
        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #F5F5F5;
        }
        ::-webkit-scrollbar {
            width: 3px;
            background-color: #F5F5F5;
        }
        ::-webkit-scrollbar-thumb {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #555;
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
