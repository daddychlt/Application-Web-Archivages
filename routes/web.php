<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PdfView;
use App\Http\Controllers\RegisterController;

Route::get('/newAccount', [RegisterController::class, 'index'])->name('register');
Route::post('/Account/store', [RegisterController::class, 'store'])->name('user.new');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function(){
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
})->group(function () {
    Route::get('/service', [ServiceController::class, 'index'])->name('service');
})->group(function () {
    Route::post('/service/store', [ServiceController::class, 'store'])->name('service.store');
})->group(function () {
    Route::get('/utilisateur', [UserController::class, 'index'])->name('user');
})->group(function () {
    Route::get('/service/{id}', [ServiceController::class, 'show'])->name('service.show');
})->group(function () {
    Route::get('/utilisateur/{id}', [UserController::class, 'show'])->name('user.show');
})->group(function () {
    Route::post('/utilisateur/store', [UserController::class, 'store'])->name('user.store');
})->group(function () {
    Route::post('/utilisateur/store_role', [UserController::class, 'store_role'])->name('user.store_role');
})->group(function () {
    Route::get('/utilisateur/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
})->group(function () {
    Route::put('/utilisateur/{id}', [UserController::class, 'update'])->name('users.update');
})->group(function () {
    Route::delete('/utilisateur/{id}', [UserController::class, 'destroy'])->name('users.destroy');
})->group(function () {
    Route::get('/document', [DocumentController::class, 'index'])->name('document');
})->group(function () {
    Route::get('/profile/{id}', [UserController::class, 'show_profile'])->name('profile');
})->group(function () {
    Route::put('/update_profile/{id}', [UserController::class, 'update_profile'])->name('user.update_profile');
})->group(function(){
    Route::post('/user/update_password', [UserController::class, 'update_password'])->name('user.update_password');
})->group(function(){
    Route::delete('/service/{id}', [ServiceController::class, 'destroy'])->name('service.destroy');
})->group(function(){
    Route::put('/service_update/{id}', [ServiceController::class, 'update'])->name('service.update');
})->group(function(){
    Route::get('/documents/{service}', [DocumentController::class, 'getDocuments'])->name('show_docs');;
})->group(function(){
    Route::get('/api/users', [UserController::class, 'users.search']);
})->group(function(){
    Route::get('/tag/{id}', [TagController::class, 'index'])->name('tag');
})->group(function(){
    Route::post('/tag/store', [TagController::class, 'store'])->name('tag.store');
})->group(function(){
    Route::get('/message', [MessageController::class, 'index'])->name('message');
})->group(function(){
    Route::get('/message/{pivotId}', [MessageController::class, 'show'])->name('message.show');
})->group(function(){
    Route::get('/messageSended/{pivotId}', [MessageController::class, 'showSend'])->name('message.showSend');
})->group(function(){
    Route::post('/indentification/store/{id}', [ServiceController::class, 'identUser'])->name('service.ident');
})->group(function(){
    Route::get('/pdf/{id}', [PdfView::class, 'index'])->name('pdf.view');
});
