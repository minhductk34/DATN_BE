<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function(){

    //Quản lý môn thi 
    Route::prefix('exam-subjects')->group(function () {
        Route::get('/', [ExamSubjectController::class,'index']);
        Route::get('/exam/{id}', [ExamSubjectController::class,'getSubjectByExam']);
        Route::post('/', [ExamSubjectController::class,'store']);
        Route::get('/{id}', [ExamSubjectController::class,'show']);
        Route::put('/{id}', [ExamSubjectController::class,'update']);
        Route::delete('/{id}', [ExamSubjectController::class,'destroy']);
        Route::put('/restore/{id}', [ExamSubjectController::class,'restore']);
    });

    
});

