<?php
// app/routes/ckeditor.php

use App\Http\Controllers\CkeditorController;
use Illuminate\Support\Facades\Route;

Route::post('ckeditor/upload', [CkeditorController::class, 'upload'])
    ->middleware(['web', 'auth'])
    ->name('ckeditor.upload');