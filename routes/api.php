<?php

use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExamContentController;
use App\Http\Controllers\TopicStructureController;
use App\Http\Controllers\ExamRoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamSubjectController;
use App\Http\Controllers\PoetryController;
use App\Http\Controllers\ListeningController;
use App\Http\Controllers\ListeningQuestionController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\ReadingQuestionController;
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

    Route::post('/login', [AdminController::class, 'login']);
    //Quản lý môn thi

    Route::prefix('exam-subjects')->group(function () {
        Route::get('/exam/{id}', [ExamSubjectController::class, 'getSubjectByExam']);
        Route::post('/', [ExamSubjectController::class, 'store']);
        Route::get('/{id}', [ExamSubjectController::class, 'show']);
        Route::put('/{id}', [ExamSubjectController::class, 'update']);
        Route::delete('/{id}', [ExamSubjectController::class, 'destroy']);
        Route::put('/restore/{id}', [ExamSubjectController::class, 'restore']);

    Route::middleware('checkToken')->prefix('exam-subjects')->group(function () {
        Route::get('/exam/{id}', [ExamSubjectController::class,'getSubjectByExam']);
        Route::post('/', [ExamSubjectController::class,'store']);
        Route::get('/{id}', [ExamSubjectController::class,'show']);
        Route::put('/{id}', [ExamSubjectController::class,'update']);
        Route::delete('/{id}', [ExamSubjectController::class,'destroy']);
        Route::put('/restore/{id}', [ExamSubjectController::class,'restore']);

        Route::post('/import', [ExamSubjectController::class, 'importExcel']);
    });
    Route::middleware('checkToken')->prefix('exam-content')->group(function () {
        //get data
        Route::get('exam-subject/{id}', [ExamContentController::class, 'getContentByExam'])->name('exam-content-byExamSubject_id');
        Route::get('/{id}', [ExamContentController::class, 'show'])->name('exam-content-byid');
        // create data
        Route::post('/', [ExamContentController::class, 'store']);
        //        Route::post('/import-excel-exam-content', [ExamContentController::class, 'importExcel']);
        //update data
        Route::put('/{id}', [ExamContentController::class, 'update']);

        //delete data
        Route::delete('/delete/{id}', [ExamContentController::class, 'destroy']);
    });


    //Exams management
    Route::prefix('exams-management')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::post('/', [ExamController::class, 'store']);
        Route::get('/{id}', [ExamController::class, 'show']);
        Route::put('/{id}', [ExamController::class, 'update']);
        Route::delete('/{id}', [ExamController::class, 'destroy']);
        Route::put('/restore/{id}', [ExamController::class, 'restore']);
        Route::post('/import', [ExamController::class, 'importExcel']);
    });
    Route::prefix('topic-structures')->group(function () {

        Route::post('/', [TopicStructureController::class, 'store']);

        Route::put('{id}', [TopicStructureController::class, 'update']);

    Route::resource('exam-room', ExamRoomController::class);
    // ca thi
    Route::resource('/poetries',PoetryController::class);
    Route::prefix('questions')->group(function () {
        //get data
        Route::get('/', [QuestionController::class, 'index']);
        Route::get('/{id}', [QuestionController::class, 'show']);
        Route::get('/{id}/versions', [QuestionController::class, 'versions']);

        // create data
        Route::post('/', [QuestionController::class, 'store']);
        Route::post('/import', [QuestionController::class, 'importExcel']);

        // update data
        Route::put('/{id}', [QuestionController::class, 'update']);
        Route::put('/update/exel', [QuestionController::class, 'updateExcel']);
        Route::patch('/restore/{id}', [QuestionController::class, 'restore']);

        //delete data
        Route::delete('/{id}', [QuestionController::class, 'destroy']);
    });

    Route::prefix('readings')->group(function () {
        //get data
        Route::get('/', [ReadingController::class, 'index']);
        Route::get('/{id}', [ReadingController::class, 'show']);

        // create data
        Route::post('/', [ReadingController::class, 'store']);
        Route::post('/import', [ReadingController::class, 'importExcel']);

        // update data
        Route::put('/{id}', [ReadingController::class, 'update']);

        //delete data
        Route::delete('/{id}', [ReadingController::class, 'destroy']);

        // reading questions
        Route::prefix('questions')->group(function () {
            //get data
            // Route::get('/', [ReadingQuestionController::class, 'index']);
            Route::get('/{id}', [ReadingQuestionController::class, 'show']);
            Route::get('/{id}/versions', [ReadingQuestionController::class, 'versions']);

            // create data
            Route::post('/', [ReadingQuestionController::class, 'store']);
            Route::post('/import', [ReadingQuestionController::class, 'importExcel']);

            // update data
            Route::put('/{id}', [ReadingQuestionController::class, 'update']);

            //delete data
            Route::delete('/{id}', [ReadingQuestionController::class, 'destroy']);
        });
    });

    Route::prefix('listenings')->group(function () {
        //get data
        Route::get('/', [ListeningController::class, 'index']);
        Route::get('/{id}', [ListeningController::class, 'show']);

        // create data
        Route::post('/', [ListeningController::class, 'store']);

        // update data
        Route::put('/{id}', [ListeningController::class, 'update']);

        //delete data
        Route::delete('/{id}', [ListeningController::class, 'destroy']);

        // reading questions
        Route::prefix('questions')->group(function () {
            //get data
            // Route::get('/', [ReadingQuestionController::class, 'index']);
            Route::get('/{id}', [ListeningQuestionController::class, 'show']);
            Route::get('/{id}/versions', [ListeningQuestionController::class, 'versions']);

            // create data
            Route::post('/', [ListeningQuestionController::class, 'store']);
            Route::post('/import', [ListeningQuestionController::class, 'importExcel']);

            // update data
            Route::put('/{id}', [ListeningQuestionController::class, 'update']);

            //delete data
            Route::delete('/{id}', [ListeningQuestionController::class, 'destroy']);
        });
    });
});

