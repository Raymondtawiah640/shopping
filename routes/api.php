<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ShoppingController;

Route::post('/login', [ShoppingController::class, 'login']);