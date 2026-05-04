<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! file_exists(storage_path('installed'))) {
        return redirect('/instalar');
    }
    return view('welcome');
});

// Alias para compatibilidade
Route::get('/register', function () {
    return redirect('/login');
})->name('register');