<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminController;

Route::get('/', function(){ return redirect('/login'); });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
 * User area
 */
Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [FileController::class, 'index'])->name('dashboard');
    Route::post('/upload', [FileController::class, 'upload'])->name('upload');
    Route::get('/files/download/{id}', [FileController::class, 'download'])->name('files.download');
});

/*
 * Admin area
 */
Route::middleware(['auth','admin'])->prefix('admin')->group(function(){
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/groups', [AdminController::class, 'createGroup'])->name('admin.groups.create');
    Route::post('/users/{id}/assign-group', [AdminController::class, 'assignGroup'])->name('admin.users.assignGroup');
    Route::post('/settings/limits', [AdminController::class, 'updateLimits'])->name('admin.settings.limits');
    Route::post('/forbidden-extensions', [AdminController::class, 'addForbiddenExtension'])->name('admin.forbidden.add');
    Route::delete('/forbidden-extensions/{id}', [AdminController::class, 'removeForbiddenExtension'])->name('admin.forbidden.remove');
});
