<?php

use App\Http\Controllers\ExamContentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function () {
    Route::prefix('exam-content')->group(function () {
        //get data
        Route::get('exam-subject/{id}', [ExamContentController::class, 'getContentByExam'])->name('exam-content-byExamSubject_id');
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
