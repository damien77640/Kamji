<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/home/insertdb','HomeController@insert')->name('insert');
Route::post('/home/supprcalendar','HomeController@supprCalendar')->name('supprCalendar');
Route::post('/home/leftCalendar','HomeController@leftCalendar')->name('leftCalendar');
Route::post('/home/sharecalendar','HomeController@shareCalendar')->name('shareCalendar');
Route::post('/home/shareviewCalendar','HomeController@shareviewCalendar')->name('shareviewCalendar');
Route::post('/home/modifCalendar','HomeController@modifCalendar')->name('modifCalendar');
Route::post('/home/modifEvent','HomeController@modifEvent')->name('modifEvent');
Route::post('/home/deleteEvent','HomeController@deleteEvent')->name('deleteEvent');
//Route::get('/home/qrcode','HomeController@qrcode')->name('qrcode');
Route::get('/home/qrcode', ['uses'=>'HomeController@qrcode', 'as'=>'qrcode']);
Route::post('/home/changeCalendar','HomeController@changeCalendar')->name('changeCalendar');

Route::get('ajaxIDFromMail', 'HomeController@getID');
Route::post('ajaxIDFromMail', 'HomeController@PostID');

Route::get('ajaxEventInfo', 'HomeController@getEventInfo');
Route::post('ajaxEventInfo', 'HomeController@PostEventInfo');

Route::get('/newCalendar', 'CalendarController@index')->name('newCalendar');
Route::post('/newCalendar/insertdb','CalendarController@insert')->name('newCalendar');
Route::post('/newCalendar', 'CalendarController@index')->name('newCalendar');

Route::get('/notification', 'NotificationController@index')->name('notification');
Route::post('/notification', 'NotificationController@index')->name('notification');
Route::post('/notification/accept', 'NotificationController@accept')->name('notifaccept');
Route::post('/notification/refuse', 'NotificationController@refuse')->name('notifrefuse');
