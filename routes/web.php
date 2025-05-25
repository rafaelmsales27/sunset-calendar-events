<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

Route::get('/', function () {
    return view('index');
});
Route::post('/', [IndexController::class, 'generate']);

Route::get('/form', [IndexController::class, 'showForm']);
Route::post('/form', [IndexController::class, 'submitForm']);