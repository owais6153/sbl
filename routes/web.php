<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FileUploadController;

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

// Login & Authentication
Route::get('/', [HomeController::class, 'index']);
Route::post('/adminlogin', [HomeController::class, 'authenticate'])->name('admin_login');

// If login
Route::middleware(['is_admin'])->group(function (){
// Logout
	Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
// Users
	Route::get('/users', [HomeController::class, 'users'])->name('user_list');
	Route::get('/users/add', [HomeController::class, 'add_user'])->name('add_user');
	Route::post('/users/adduser', [HomeController::class, 'addusers'])->name('addusers');
	Route::get('/users/edit/{id}', [HomeController::class, 'edit_user'])->name('edit_user');	
	Route::post('/users/edituser', [HomeController::class, 'edituser'])->name('edituser');
	Route::get('/users/delete{id}', [HomeController::class, 'deleteuser'])->name('deleteuser');
// File Upload
	Route::get('/files/import', [FileUploadController::class, 'importFiles'])->name('import_files');
	Route::post('/files/import/save', [FileUploadController::class, 'saveImportFiles'])->name('saveImportFiles');

});
