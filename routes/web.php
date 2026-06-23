<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Clientes;
use App\Livewire\Ventas;
use App\Livewire\Productos;
use App\Livewire\Abonos;

// 🔄 Redirección automática de la raíz al Login
// 🔄 Redirección automática de la raíz al Login con el nombre 'home'
Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    // Rutas limpias (ya heredan el middleware del grupo)
    Route::get('/clientes', Clientes::class)->name('clientes');
    Route::get('/ventas', Ventas::class)->name('ventas');
    Route::get('/productos', Productos::class)->name('productos');
    Route::get('/abonos', Abonos::class)->name('abonos');
});

require __DIR__.'/settings.php';