<?php

use Illuminate\Support\Facades\Route;

// Public Routes (Visitor-facing, no authentication required)
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/species', function () {
    if (auth()->check()) {
        return redirect('/admin/dashboard');
    }
    return view('public.species-list');
})->name('species.index');

Route::get('/species/{species}', function (\App\Models\Species $species) {
    if (auth()->check()) {
        return redirect('/admin/dashboard');
    }
    return view('public.species-detail', ['species' => $species]);
})->name('species.show');

Route::get('/discover-butterflies', function () {
    if (auth()->check()) {
        return redirect('/admin/dashboard');
    }
    return view('public.discover-butterflies');
})->name('discover.index');

Route::get('/plants/{plant}', function (\App\Models\Plant $plant) {
    if (auth()->check()) {
        return redirect('/admin/dashboard');
    }
    return view('public.plant-detail', ['plant' => $plant]);
})->name('plants.show');

Route::get('/map', function () {
    if (auth()->check()) {
        return redirect('/admin/dashboard');
    }
    return view('public.map');
})->name('map.index');


// Admin Routes (authenticated)
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

    Route::get('regions', function () {
        return view('admin.regions');
    })->name('regions.index');

    Route::get('habitats', function () {
        return view('admin.habitats');
    })->name('habitats.index');

    Route::get('plants', function () {
        return view('admin.plants');
    })->name('plants.index');

    Route::get('life-forms', function () {
        return view('admin.life-forms');
    })->name('life-forms.index');

    Route::get('threat-categories', function () {
        return view('admin.threat-categories');
    })->name('threat-categories.index');

    Route::get('distribution-areas', function () {
        return view('admin.distribution-areas');
    })->name('distribution-areas.index');

    Route::get('users', function () {
        return view('admin.users');
    })->name('user.index');

    Route::get('species/{species}/generations', function ($speciesId) {
        return view('admin.generations', ['speciesId' => $speciesId]);
    })->name('generations.index');
    Route::get('species/{species}/speciesDistributionAreas', function ($speciesId) {
        return view('admin.species-distribution-areas', ['speciesId' => $speciesId]);
    })->name('speciesDistributionAreas.index');
});

Route::middleware(['auth'])->get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->name('dashboard');

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login.store');

Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
