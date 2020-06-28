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
        .panel-body {
           position: relative;
           height: 700px
        }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Chats</div>

                <div class="border panel-body" id="chatContainer">
                  <chat-messages>
                        :user="{{ Auth::user() }}"
                  </chat-messages>
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
    <script>      
       window.addEventListener("resize", onResize);
       onResize();
       function onResize() {
         var calcheight = window.innerHeight - document.getElementById("appBar").offsetHeight - 115;
         document.getElementById("chatContainer").style.height = calcheight + "px";
       }
    </script>      
@endsection
