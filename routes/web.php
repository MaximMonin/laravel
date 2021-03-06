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
Route::get('/initstore', 'StoreController@initstore')->name('store');

Route::get('/video', 'VideoController@index')->name('video');
Route::get('/documentation', 'DocumentationController@index0')->name('documentation');
Route::get('/documentation/{topic}', 'DocumentationController@index');
Route::get('/contact', 'ContactController@index')->name('contact');

Route::get('/user/upload', 'UserUploadController@show')->middleware('verified')->name('fileupload');
Route::post('/upload', 'UploadController@upload0')->name('upload');
Route::post('/upload/delete', 'UploadController@uploaddelete0')->name('uploaddelete');
Route::post('/upload/{storage}', 'UploadController@upload');
Route::post('/upload/{storage}/delete', 'UploadController@uploaddelete');
Route::get('/download/{storage}/{file}', 'DownloadController@download')->where('file', '.*');
Route::get('/file/{storage}/{file}', 'DownloadController@loadfile')->where('file', '.*');

Route::get('/user/chat', 'ChatController@index')->middleware('verified')->name('chat');
Route::get('/user/chat/messages', 'ChatController@fetchMessages');
Route::post('/user/chat/messages', 'ChatController@sendMessage');

Route::get('/user/photos', 'FilesController@fetchPhotos');
Route::get('/user/videos', 'FilesController@fetchVideos');
Route::get('/user/docs', 'FilesController@fetchDocs');
