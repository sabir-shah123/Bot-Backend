<?php

use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Sabir\ChatGPT\ChatGPT;

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

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/', 'DashboardController@dashboard')->name('dashboard');
    Route::get('/profile', 'DashboardController@profile')->name('profile');
    Route::post('/profile-save', 'DashboardController@general')->name('profile.save');
    Route::post('/password-save', 'DashboardController@changePassword')->name('password.save');
    Route::post('/email-change', 'DashboardController@changeEmail')->name('email.save');

    Route::prefix('users')->name('user.')->controller(UserController::class)->group(function () {
        Route::get('/list', 'list')->name('list');
        Route::get('/add', 'add')->name('add');
        Route::get('/edit/{id?}', 'edit')->name('edit');
        Route::post('/save/{id?}', 'save')->name('save');
        Route::get('/delete/{id?}', 'delete')->name('delete');
        Route::get('/status/{id?}', 'status')->name('status');
    });

    Route::prefix('settings')->name('setting.')->controller(SettingController::class)->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::post('/save', 'save')->name('save');
    });

    Route::prefix('authorization')->name('authorization.')->group(function () {
        Route::get('/crm/oauth/callback', [SettingController::class, 'goHighLevelCallback'])->name('gohighlevel.callback');
    });
});

Route::get('/chat', function () {
    $s = new ChatGPT();
    $file_url = public_path('hello.m4a');
    $audioData = file_get_contents($file_url);
    $audioData = base64_encode($audioData);
    $s->chat($audioData);
    // dd($s->chat('hi, how are you?'));
});

Route::get('/audio-read', function () {
    $file = public_path('audio.wav');
    $s = new ChatGPT();
    dd($s->getTextFromAudio($file));

});
