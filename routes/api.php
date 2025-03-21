<?php

use App\Http\Controllers\PromocionalController;
use Illuminate\Support\Facades\Route;

Route::apiResource('promocionales-destacados', PromocionalController::class);
