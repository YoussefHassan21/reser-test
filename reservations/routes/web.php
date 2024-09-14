<?php

use App\Http\Controllers\SectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\SectionFieldController;
use App\Http\Controllers\TypeReservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Route pour le tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes pour le profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour les formulaires
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');
    Route::get('/forms/{form}', [FormController::class, 'show'])->name('forms.show'); // Correction : Utilise FormController
    Route::post('/forms/{form}/update-section-order', [FormController::class, 'updateSectionOrder'])->name('forms.updateSectionOrder');
    Route::post('/forms/{form}/update-field-order', [FormController::class, 'updateFieldOrder'])->name('forms.updateFieldOrder');
    Route::post('/forms/update-unit-order', [FormController::class, 'updateUnitOrder'])->name('forms.updateUnitOrder'); // Ajout pour l'ordre des unités
    Route::delete('/forms/{form}/fields/{field}', [FormController::class, 'destroyField'])->name('fields.destroy');
    Route::delete('/forms/{form}/sections/{section}', [FormController::class, 'destroySection'])->name('sections.destroy');
    Route::post('/forms/update-unit-order', [FormController::class, 'updateUnitOrder'])->name('forms.updateUnitOrder');
    Route::post('/sections/{section}/update-element-order', [SectionController::class, 'updateElementOrder'])->name('section.updateElementOrder');

    // Routes pour les sections
    Route::get('/forms/{form}/sections/create', [SectionController::class, 'create'])->name('sections.create');
    Route::post('/forms/{form}/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show');
    Route::post('/sections/{section}/update-field-order', [SectionController::class, 'updateFieldOrder'])->name('section.updateFieldOrder');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

    // Routes pour les champs des formulaires
    Route::get('/forms/{form}/fields/create', [FieldController::class, 'create'])->name('fields.create');
    Route::post('/forms/{form}/fields', [FieldController::class, 'store'])->name('fields.store');
    Route::delete('/forms/{form}/fields/{field}', [FieldController::class, 'destroy'])->name('fields.destroy');

    // Routes pour les champs des sections
    Route::get('/sections/{section}/fields/create', [SectionFieldController::class, 'create'])->name('section.fields.create');
    Route::post('/sections/{section}/fields', [SectionFieldController::class, 'store'])->name('section.fields.store');
    Route::delete('/sections/{section}/fields/{field}', [SectionFieldController::class, 'destroy'])->name('section.fields.destroy');

    // Routes pour les sous-sections
    Route::get('/sections/{section}/create-child', [SectionController::class, 'createChild'])->name('sections.createChild');
    Route::post('/sections/{section}/store-child', [SectionController::class, 'storeChild'])->name('sections.storeChild');
    Route::post('/sections/{section}/update-child-section-order', [SectionController::class, 'updateChildSectionOrder'])->name('sections.updateChildSectionOrder');

    // Routes pour les types de réservation
    Route::get('/types_reservation', [TypeReservationController::class, 'index'])->name('types_reservation.index');
    Route::get('/types_reservation/create', [TypeReservationController::class, 'create'])->name('types_reservation.create');
    Route::post('/types_reservation', [TypeReservationController::class, 'store'])->name('types_reservation.store');
    Route::get('/types_reservation/{type_reservation}', [TypeReservationController::class, 'show'])->name('types_reservation.show');
    Route::delete('/types_reservation/{type_reservation}', [TypeReservationController::class, 'destroy'])->name('types_reservation.destroy');
    Route::get('/types_reservation/{type_reservation}/edit', [TypeReservationController::class, 'edit'])->name('types_reservation.edit');
    Route::put('/types_reservation/{type_reservation}', [TypeReservationController::class, 'update'])->name('types_reservation.update');
    Route::post('/sections/{section}/update-unit-order', [SectionController::class, 'updateUnitOrder'])->name('sections.updateUnitOrder');
    Route::post('/sections/{section}/update-main-section-order', [SectionController::class, 'updateMainSectionOrder'])->name('section.updateMainSectionOrder');
    Route::post('/forms/{form}/update-main-element-order', [FormController::class, 'updateMainElementOrder'])->name('forms.updateMainElementOrder');
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

});

require __DIR__.'/auth.php';
Route::get('/login', function () {
    return view('auth.login'); // Assurez-vous que 'auth.login' est le chemin correct de votre vue
})->name('login');