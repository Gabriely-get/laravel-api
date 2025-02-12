<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', [UserController::class, 'all']);

Route::post('/user', [UserController::class, 'store']);
