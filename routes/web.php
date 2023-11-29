<?php

use App\Http\Controllers\ClimaTempoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ClimaTempoController::class, 'index'])->name('weather.index');
Route::post('/search', [ClimaTempoController::class, 'search'])->name('clima.search');


