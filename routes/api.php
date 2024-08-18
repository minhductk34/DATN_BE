<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExamContentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamSubjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::prefix('admin')->group(function () {

    Route::post('/login', [AdminController::class, 'login']);
    //Quản lý môn thi 
    Route::prefix('exam-subjects')->group(function () {
        Route::get('/exam/{id}', [ExamSubjectController::class, 'getSubjectByExam']);
        Route::post('/', [ExamSubjectController::class, 'store']);
        Route::get('/{id}', [ExamSubjectController::class, 'show']);
        Route::put('/{id}', [ExamSubjectController::class, 'update']);
        Route::delete('/{id}', [ExamSubjectController::class, 'destroy']);
        Route::put('/restore/{id}', [ExamSubjectController::class, 'restore']);
        Route::post('/import', [ExamSubjectController::class, 'importExcel']);
    });

    Route::prefix('exam-contents')->group(function () {
        //get data
        Route::get('exam-subject/{id}', [ExamContentController::class, 'getContentByExam']);
        Route::get('/{id}', [ExamContentController::class, 'show'])->name('exam-content-byid');
        // create data
        Route::post('/', [ExamContentController::class, 'store']);
        Route::post('/import-excel-exam-content', [ExamContentController::class, 'importExcel']);
        //update data
        Route::put('/{id}', [ExamContentController::class, 'update']);
        //delete data
        Route::delete('/delete/{id}', [ExamContentController::class, 'destroy']);
    });
});
