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

use Telegram\Bot\Api;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/webhook', 'TelegramController@index');
Route::post('/webhook', 'TelegramController@index');

Route::group(['prefix' => 'admin'], function () {
    Route::get('run-crawling','CommandController@runCrawling')->name('run-crawling');
    Route::get('run-sync','CommandController@runSync')->name('run-sync');
    Route::get('logs','LogController@index')->name('logs');

});

//Route::post('/{token}/webhook', function () {
//    $updates = Api::getWebhookUpdates();
//
//    return 'ok';
//});
