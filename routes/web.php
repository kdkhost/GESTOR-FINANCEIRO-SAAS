<?php

use Illuminate\Support\Facades\Route;

// Rota raiz: redireciona para instalador se não instalado, ou para o dashboard
Route::get('/', function () {
    if (file_exists(storage_path('installed'))) {
        return redirect('/admin/dashboard');
    }
    return redirect('/instalar');
});
