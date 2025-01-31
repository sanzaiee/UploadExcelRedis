<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
  return view('upload.upload');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/upload/mobile-numbers',function(){
        return view('upload.upload');
    });
    Route::post('/upload/mobile-numbers', [UploadController::class, 'upload'])->name('file.upload');
    Route::get('/mobile-numbers',[UploadController::class,'index'])->name('mobile.numbers');
    Route::delete('/mobile-numbers/delete',[UploadController::class,'mobileNumbersDestroy'])->name('mobile.destroy');
    Route::get('/activity-log',[UploadController::class,'activityLog'])->name('activity.log');
    Route::delete('/activity-log/delete',[UploadController::class,'activityLogDestroy'])->name('activity.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
