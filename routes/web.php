<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\InventoryLocationTrackingController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\SkippedItemIdentifiersController;
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
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

// If login
Route::middleware(['is_admin'])->group(function (){
// Logout
	Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
// Users
	Route::get('/users', [HomeController::class, 'users'])->name('user_list')->middleware('can:view_all_users');
	Route::get('/users/add', [HomeController::class, 'add_user'])->name('add_user')->middleware('can:user_add');
	Route::post('/users/adduser', [HomeController::class, 'addusers'])->name('addusers')->middleware('can:user_add');
	Route::get('/users/edit/{id}', [HomeController::class, 'edit_user'])->name('edit_user')->middleware('can:user_update');	
	Route::post('/users/edituser', [HomeController::class, 'edituser'])->name('edituser')->middleware('can:user_update');
	Route::get('/users/delete{id}', [HomeController::class, 'deleteuser'])->name('deleteuser')->middleware('can:user_delete');
	Route::get('/users/allUsers', [HomeController::class, 'userDisplay'])->name('userDisplay');
// Roles
	Route::get('/roles', [RolesController::class, 'index'])->name('role_list')->middleware('can:view_all_role');
	Route::get('/roles/add', [RolesController::class, 'create'])->name('add_role')->middleware('can:role_add');
	Route::post('/roles/add', [RolesController::class, 'store'])->name('store_role')->middleware('can:role_add');
	Route::get('/roles/edit/{id}', [RolesController::class, 'edit'])->name('edit_role')->middleware('can:role_update');	
	Route::post('/roles/update/{id}', [RolesController::class, 'update'])->name('update_role')->middleware('can:role_update');
	Route::get('/roles/delete{id}', [RolesController::class, 'destroy'])->name('delete_role')->middleware('can:role_delete');
	Route::get('/roles/all_roles', [RolesController::class, 'allRoles'])->name('allRoles');

// File Upload
	Route::get('/files/import', [FileUploadController::class, 'importFiles'])->name('import_files')->middleware('can:inventory_import');
	Route::post('/files/import/save', [FileUploadController::class, 'saveImportFiles'])->name('saveImportFiles');
	// On hand
	Route::get('/files/onhand', [FileUploadController::class, 'inventoryOnhand'])->name('inventoryOnhand')->middleware('can:inventory_view_on_hand');
	Route::get('/files/onhand/get', [FileUploadController::class, 'getOnHandList'])->name('getOnHandList');
	// On Recieve
	Route::get('/files/onrecive', [FileUploadController::class, 'inventoryOnRecive'])->name('inventoryOnRecive')->middleware('can:inventory_view_on_receive');
	Route::get('/files/onrecive/get', [FileUploadController::class, 'getOnReciveList'])->name('getOnReciveList');
// Inventory Location
	Route::get('/inventory', [InventoryLocationTrackingController::class, 'getDataByItems'])->name('inventory')->middleware('can:inventory_location');
	Route::get('/inventory-by-barcode', [InventoryLocationTrackingController::class, 'index'])->name('inventoryByBarcode')->middleware('can:inventory_location');	
	Route::get('/inventory/getdetail/{barcode}', [InventoryLocationTrackingController::class, 'getInventoryDetails'])->name('getInventoryDetails');
	Route::get('/inventory/detail/{barcode}', [InventoryLocationTrackingController::class, 'getInventoryDetailsView'])->name('getInventoryDetailsView');
	Route::get('/inventory/add', [InventoryLocationTrackingController::class, 'create'])->name('addInventory')->middleware('can:scan_inventroy');
	Route::get('/inventory/deletemove/{id}', [InventoryLocationTrackingController::class, 'deletemove'])->name('deletemove');
// Items
	Route::get('/items', [ItemsController::class, 'index'])->name('listitems')->middleware('can:view_all_item');	
	Route::get('/items/getItems', [ItemsController::class, 'getItems'])->name('getItems');
// Skipped Items
	Route::get('/skipped-items', [SkippedItemIdentifiersController::class, 'index'])->name('listSkippedItems')->middleware('can:item_skip');	
	Route::get('/skipped-items/getItems', [SkippedItemIdentifiersController::class, 'getSkippedItems'])->name('getSkippedItems');

});


// AJAX REQUEST
Route::middleware(['validate_ajax'])->group(function (){
	Route::post('/inventory/getExiprationDateAndQuantity', [InventoryLocationTrackingController::class, 'getExiprationDateAndQuantity'])->name('getExiprationDateAndQuantity');
	Route::post('/inventory/upload', [InventoryLocationTrackingController::class, 'uploadImage'])->name('uploadImage');
	Route::post('/inventory/upload/remove', [InventoryLocationTrackingController::class, 'removeImage'])->name('removeImage');
	Route::post('/inventory/getlocationbybarcode', [InventoryLocationTrackingController::class, 'getlocationbybarcode'])->name('getlocationbybarcode');
	Route::post('/inventory/save', [InventoryLocationTrackingController::class, 'saveInventory'])->name('saveInventory');
});