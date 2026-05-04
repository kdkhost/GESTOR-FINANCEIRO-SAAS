<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! file_exists(storage_path('installed'))) {
        return redirect('/instalar');
    }

    return view('welcome');
});
