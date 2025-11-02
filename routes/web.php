<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('species', function () {
        return view('admin.species');
    })->name('species.index');

    Route::get('families', function () {
        return view('admin.families');
    })->name('families.index');

    Route::get('habitats', function () {
        return view('admin.habitats');
    })->name('habitats.index');

    Route::get('plants', function () {
        return view('admin.plants');
    })->name('plants.index');

    Route::get('life-forms', function () {
        return view('admin.life-forms');
    })->name('life-forms.index');

    Route::get('distribution-areas', function () {
        return view('admin.distribution-areas');
    })->name('distribution-areas.index');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('guest');

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
