<?php
use App\Http\Controllers\Frontend\CMSController;
use Illuminate\Support\Facades\Route;

//Modular Page
Route::get('cms/{slug}', [CMSController::class, 'index']);
