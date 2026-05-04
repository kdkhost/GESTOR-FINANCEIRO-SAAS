<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (file_exists(storage_path('installed'))) {
        return redirect('/admin/dashboard');
    }
    return redirect('/instalar');
});

// Alias para compatibilidade
Route::get('/register', function () {
    return redirect('/login');
})->name('register');