<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\InventoryLocationTrackingController;
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
	// On hand
	Route::get('/files/onhand', [FileUploadController::class, 'inventoryOnhand'])->name('inventoryOnhand');
	Route::get('/files/onhand/get', [FileUploadController::class, 'getOnHandList'])->name('getOnHandList');
	// On Recieve
	Route::get('/files/onrecive', [FileUploadController::class, 'inventoryOnRecive'])->name('inventoryOnRecive');
	Route::get('/files/onrecive/get', [FileUploadController::class, 'getOnReciveList'])->name('getOnReciveList');
// Inventory Location
	Route::get('/inventory', [InventoryLocationTrackingController::class, 'index'])->name('inventory');	
	Route::get('/inventory/detail/{id}', [InventoryLocationTrackingController::class, 'getInventoryDetails'])->name('getInventoryDetails');
	Route::post('/inventory/save', [InventoryLocationTrackingController::class, 'saveInventory'])->name('saveInventory');
	Route::get('/inventory/add', [InventoryLocationTrackingController::class, 'create'])->name('addInventory');
	Route::post('/inventory/upload', [InventoryLocationTrackingController::class, 'uploadImage'])->name('uploadImage');
	Route::post('/inventory/upload/remove', [InventoryLocationTrackingController::class, 'removeImage'])->name('removeImage');



});
