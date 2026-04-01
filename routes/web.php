<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Page de test
Route::get('/test', function () {
    return '<h1>HealthSys fonctionne !</h1>';
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Routes patients (CRUD complet)
Route::resource('patients', PatientController::class)->middleware(['auth']);

// Routes d'authentification Breeze
require __DIR__.'/auth.php';