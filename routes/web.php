<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;
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
    return view('index');
})->name('home');

Route::resource('urls', urlController::class)->only([
    'index', 'show', 'store'
]);

Route::post('/urls/{id}/checks', [UrlController::class, 'check'])->name('url.check');
