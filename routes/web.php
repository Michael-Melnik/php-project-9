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

Route::post('/urls', [UrlController::class, 'store'])->name('url.store');
Route::get('/urls/{id}', [UrlController::class, 'show'])->name('url.show');
Route::get('/urls', [UrlController::class, 'showAll'])->name('url.showAll');
//Route::resource('url', 'UrlController')->only([
//    'showAll', 'show', 'store'
//]);
