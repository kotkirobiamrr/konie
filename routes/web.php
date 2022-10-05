<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return redirect ('login');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'] )-> name('home')->middleware('auth');
Route::get('/contact', [App\Http\Controllers\ContactSiteController::class, 'index'] )-> name('contact');

Route::get('/myAccount', [App\Http\Controllers\UserAccountSettingsController::class, 'index'] )-> name('accountsettings')->middleware('auth');
Route::post('/myAccount/general', [App\Http\Controllers\UserAccountSettingsController::class, 'edit_general'] )-> name('accountsettings.general')->middleware('auth');
Route::post('/myAccount/email', [App\Http\Controllers\UserAccountSettingsController::class, 'edit_email'] )-> name('accountsettings.email')->middleware('auth');
Route::post('/myAccount/password', [App\Http\Controllers\UserAccountSettingsController::class, 'edit_password'] )-> name('accountsettings.password')->middleware('auth');
Route::post('/myAccount/delete', [App\Http\Controllers\UserAccountSettingsController::class, 'delete'] )-> name('accountsettings.delete')->middleware('auth');

////////////////////////////////////
//
//	trasy dla rezerwacji

Route::get('/getReservations/{date}', [App\Http\Controllers\ReservationsController::class, 'show'] )->middleware('auth');
Route::post('/addReservation', [App\Http\Controllers\ReservationsController::class, 'store'] )->name('reservations.add')->middleware('auth');
Route::get('/deleteReservation/{id}', [App\Http\Controllers\ReservationsController::class, 'destroy'] )->name('reservations.delete')->middleware('auth');


/////////////////////////////////////
//
//	trasy dla logowania i rejestracji

Route::get('notlogged', function() {	//z middleware, przy próbie wejścia na strony, do których nie ma dostępu
	return redirect('login') -> with('message', 'Musisz się zalogować, by mieć dostęp do systemu.');
})->name('notlogged');

Auth::routes([	//brak możliwości samodzielnego zarejestrowania się
	'register' => false,
	'verify' => false
]);


////////////////////////////////////
//
//	trasy dla administratora

Route::get('/users', [App\Http\Controllers\AdminUsersController::class, 'index'] )->name('admin_users')->middleware('auth');
Route::post('/users/edit', [App\Http\Controllers\AdminUsersController::class, 'edit'] )->name('admin_users.edit')->middleware('auth');
Route::get('/users/delete/', [App\Http\Controllers\AdminUsersController::class, 'index'] )->name('admin_users.delete_view')->middleware('auth');
Route::get('/users/delete/{id}', [App\Http\Controllers\AdminUsersController::class, 'destroy'] )->name('admin_users.delete')->middleware('auth');

Route::get('/areas', [App\Http\Controllers\AdminAreasController::class, 'index'] )->name('admin_areas')->middleware('auth');
Route::post('/areas/edit', [App\Http\Controllers\AdminAreasController::class, 'edit'] )->name('admin_areas.edit')->middleware('auth');
Route::get('/areas/delete/', [App\Http\Controllers\AdminAreasController::class, 'index'] )->name('admin_areas.delete_view')->middleware('auth');
Route::get('/areas/delete/{id}', [App\Http\Controllers\AdminAreasController::class, 'destroy'] )->name('admin_areas.delete')->middleware('auth');

Route::get('/settings', [App\Http\Controllers\AdminSettingsController::class, 'index'] )->name('admin_settings')->middleware('auth');
Route::post('/settings', [App\Http\Controllers\AdminSettingsController::class, 'edit'] )->name('admin_settings.edit')->middleware('auth');
