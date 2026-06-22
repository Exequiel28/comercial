<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Clientes;
use App\Livewire\Ventas;
use App\Livewire\Productos;
use App\Livewire\Abonos;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/clientes', Clientes::class)->middleware(['auth', 'verified'])->name('clientes');
    Route::get('/ventas', Ventas::class)->middleware(['auth', 'verified'])->name('ventas');
    Route::get('/productos', Productos::class)->middleware(['auth', 'verified'])->name('productos');
    Route::get('/abonos', Abonos::class)->middleware(['auth', 'verified'])->name('abonos');

});

require __DIR__.'/settings.php';
