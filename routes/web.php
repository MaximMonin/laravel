<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('locale/{locale}', function ($locale) {
    Session::put('locale', $locale);

    return redirect()->back();
})->name('locale');

Auth::routes(['verify' => true]);

Route::get('/', 'WelcomeController@index');
Route::get('/home', 'HomeController@index')->middleware('verified')->name('home');
Route::get('/profile', 'ProfileController@show')->middleware('verified')->name('profile');
Route::post('/profile', 'ProfileController@update')->middleware('verified')->name('saveprofile');
Route::post('/phone/sendsms', 'PhoneController@sendsms')->middleware('check.session')->name('sendsms');;
Route::post('/phone/verify', 'PhoneController@verify')->middleware('check.session')->name('phoneverify');

Route::get('/video', 'VideoController@index')->name('video');
Route::get('/documentation', 'DocumentationController@index0')->name('documentation');
Route::get('/documentation/{topic}', 'DocumentationController@index');
Route::get('/contact', 'ContactController@index')->name('contact');

Route::get('/user/upload', 'UserUploadController@show')->middleware('verified')->name('fileupload');
Route::post('/upload', 'UploadController@upload')->name('upload');
Route::post('/upload/delete', 'UploadController@uploaddelete')->name('uploaddelete');

Route::get('/user/chat', 'ChatController@show')->middleware('verified')->name('chat');
Route::get('/call-event', function () {
    event(new \App\Events\ChatMessage("Heloo how are you"));
});
