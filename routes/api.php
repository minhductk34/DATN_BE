<?php

use App\Http\Controllers\PasswordController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidateQuestionController;
use App\Http\Controllers\ExamContentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\TopicStructureController;
use App\Http\Controllers\ExamRoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamSubjectController;
use App\Http\Controllers\ExamSubjectDetailsController;
use App\Http\Controllers\ExamSessionController;
use App\Http\Controllers\LecturersController;
use App\Http\Controllers\ListeningController;
use App\Http\Controllers\ListeningQuestionController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\ReadingQuestionController;
use App\Http\Controllers\CustomBroadcastController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\RoomStatusController;
use App\Models\Candidate;

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

Route::get('/logout', [AdminController::class, 'logout']);

Route::prefix('admin')->group(function () {

    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout']);
    //kỳ thi
    Route::middleware('checkToken')->prefix('/exam')->group(function () {
        Route::resource('/', ExamController::class);
        Route::get('/exam-rooms-in-exams/{id}', [ExamController::class, 'getExamRoomsInExams']);
        Route::get('/get-all-with-status-true', [ExamController::class, 'getAllWithStatusTrue']);
        Route::get('/exam-with-exam-subject', [ExamController::class, 'getALLExamsWithExamSubjects']);
        Route::get('/exam-subjects-with-content/{exam_id}', [ExamController::class, 'getExamSubjectsWithContent']);
        Route::get('/exam-with-exam-subject/{id}', [ExamController::class, 'getALLExamsWithExamSubjectsById']);
        Route::get('/exam-with-subject/{id}/{idcode}', [ExamController::class, 'getALLExamsSubjectsById']);
    });
    //Quản lý môn thi
    Route::middleware('checkToken')->prefix('exam-subjects')->group(function () {
        Route::get('/exam/{id}', [ExamSubjectController::class, 'getSubjectByExam']);
        Route::post('/', [ExamSubjectController::class, 'store']);
        Route::get('/{id}', [ExamSubjectController::class, 'show']);
        Route::get('/', [ExamSubjectController::class, 'index']);
        Route::put('/{id}', [ExamSubjectController::class, 'update']);
        Route::delete('/{id}', [ExamSubjectController::class, 'destroy']);
        Route::put('/restore/{id}', [ExamSubjectController::class, 'restore']);
        Route::put('/update-status/{id}', [ExamSubjectController::class, 'updateStatus']);
        Route::post('/export-excel', [ExamSubjectController::class, 'exportExcel']);
        Route::post('/import-excel', [ExamSubjectController::class, 'importExcel']);
    });
    Route::middleware('checkToken')->prefix('exam-content')->group(function () {
        //get data
        Route::get('exam-subject/{id}', [ExamContentController::class, 'getContentByExam'])->name('exam-content-byExamSubject_id');
        Route::get('/{id}', [ExamContentController::class, 'show'])->name('exam-content-byid');
        Route::get('/{id}/question-counts', [ExamContentController::class, 'getQuestionCounts'])->name('question-count-by-level');
        Route::post('/', [ExamContentController::class, 'store']);
        Route::post('/import-excel-exam-content', [ExamContentController::class, 'importExcel']);
        Route::put('/{id}', [ExamContentController::class, 'update']);

        Route::put('/status/{id}', [ExamContentController::class, 'updateStatus']);
        Route::delete('/delete/{id}', [ExamContentController::class, 'destroy']);
    });
    //Exams management
    Route::middleware('checkToken')->prefix('exams-management')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::post('/', [ExamController::class, 'store']);
        Route::get('/{id}', [ExamController::class, 'show']);
        Route::put('/{id}', [ExamController::class, 'update']);
        Route::delete('/{id}', [ExamController::class, 'destroy']);
        Route::put('/restore/{id}', [ExamController::class, 'restore']);
        Route::post('/import', [ExamController::class, 'importExcel']);
    });
    Route::middleware('checkToken')->prefix('topic-structures')->group(function () {
        // Tạo mới topic structure
        Route::post('/', [TopicStructureController::class, 'store']);

        // Cập nhật topic structure
        Route::put('{id}', [TopicStructureController::class, 'update']);

        // Lấy thông tin topic structure theo ID
        Route::get('{id}', [TopicStructureController::class, 'show']);

        // Lấy thông tin topic structure theo exam_subject_id
        Route::get('exam-subject/{exam_subject_id}', [TopicStructureController::class, 'showByExamSubjectId']);

        Route::get('/total/{id}', [TopicStructureController::class, 'getTotal']);
    });
    Route::middleware('checkToken')->resource('exam-room', ExamRoomController::class);
    Route::middleware('checkToken')->prefix('exam-room')->group(function () {
        Route::get('/detail/{id}', [ExamRoomController::class, 'showDetail']);
        Route::get('/data-select-update/{exam_room_id}/{exam_subject_id}', [ExamRoomController::class, 'dataSelectUpdate']); // Thêm route này
        Route::get('/by-exam/{id}', [ExamRoomController::class, 'show']);
    });
    Route::middleware('checkToken')->resource('lecturer', LecturersController::class);
    // ca thi
    Route::middleware('checkToken')->resource('/exam-session', ExamSessionController::class);

    Route::middleware('checkToken')->prefix('questions')->group(function () {
        //get data
        Route::get('/exam-content/{id}', [QuestionController::class, 'index']);
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

        //excel
        Route::post('/export-excel', [QuestionController::class, 'exportExcel']);
        Route::post('/import-excel', [QuestionController::class, 'importExcel']);
    });

    Route::middleware('checkToken')->prefix('candidate')->group(function () {
        Route::get('/getAll', [CandidateController::class, 'index']);
        Route::get('/detail-candidate/{id}', [CandidateController::class, 'show']);
        Route::post('/export-excel-password-candidate', [CandidateController::class, 'exportExcel']);
        Route::post('/store', [CandidateController::class, 'store']);
        Route::post('/import-excel-candidate', [CandidateController::class, 'importExcel']);
        Route::put('/update/{id}', [CandidateController::class, 'update']);
        Route::delete('/delete/{id}', [CandidateController::class, 'destroy']);
        Route::get('/exam-room/{exam_room_id}', [CandidateController::class, 'countCandidateForExamRoom']);
        Route::get('/exam-room/candidate-in-exam-room/{exam_room_id}', [CandidateController::class, 'CandidateInExamRoom']);
    });
    Route::middleware('checkToken')->post('active/toggle', [CandidateController::class, 'toggleActiveStatus']);

    Route::middleware('checkToken')->prefix('/password')->group(function () {
        Route::post('/actionExport', [PasswordController::class, 'actionExport']);
    });
    Route::middleware('checkToken')->prefix('readings')->group(function () {
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
        Route::middleware('checkToken')->prefix('questions')->group(function () {
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

    Route::middleware('checkToken')->prefix('listenings')->group(function () {
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
        Route::middleware('checkToken')->prefix('questions')->group(function () {
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

    Route::middleware('checkToken')->prefix('exam-subject-details')->group(function () {
        Route::get('/', [ExamSubjectDetailsController::class, 'index']);
        Route::get('/{id}', [ExamSubjectDetailsController::class, 'show']);
        Route::get('/exam-subject/{exam_subject_id}', [ExamSubjectDetailsController::class, 'showByExamSubjectId']);
        Route::post('/', [ExamSubjectDetailsController::class, 'store']);
        Route::put('/{id}', [ExamSubjectDetailsController::class, 'update']);
        Route::delete('/{id}', [ExamSubjectDetailsController::class, 'destroy']);
    });

    Route::middleware('checkToken')->prefix('client')->group(function () {
        Route::prefix('questions')->group(function () {
            Route::get('manageQuestions', [QuestionController::class, 'dataOptions']);
            Route::post('manageQuestions/{examId}/{examSubjectId}', [QuestionController::class, 'dataQuestion']);
        });
    });

    Route::middleware('checkToken')->prefix('points')->group(function () {
        Route::get('student/{idcode}/exam/{examId}', [PointController::class, 'getStudentPointsByExam']);
    });

    Route::middleware('checkToken')->prefix('reports')->group(function () {
        Route::get('byExam/{id}',[CandidateController::class, 'reportByIdExam']);
        Route::get('bySubject/{id}/subject/{subject_id}',[CandidateController::class, 'reportByIdSubject']);
        Route::get('byRoom/{id}/room/{room_id}',[CandidateController::class, 'reportByIdRoom']);
    });
});
Route::post('/client/login', [CandidateController::class, 'login']);
Route::post('/client/logout', [CandidateController::class, 'logout']);
Route::middleware('checkToken')->prefix('client')->group(function () {

    Route::post('/exam', [CandidateQuestionController::class, 'exam']);

    Route::post('/update_time', [CandidateQuestionController::class, 'update_time']);

    Route::get('/info/{id}', [CandidateController::class, 'info']);

    Route::get('/scoreboard/{id}', [CandidateQuestionController::class, 'scoreboard']);
    Route::get('/scoreboard/{id}/{subject}', [CandidateQuestionController::class, 'scoreboardBySubject']);
});


Route::middleware('checkToken')->prefix('exam')->group(function () {
    Route::post('/submit', [CandidateQuestionController::class, 'update']);

    Route::post('/finish', [CandidateQuestionController::class, 'finish']);

    Route::post('/update-status/{id}', [CandidateQuestionController::class, 'updateStatus']);

    Route::get('/scoreboard/{id}', [CandidateController::class, 'info']);

    Route::get('/history/{id}', [CandidateController::class, 'info']);

    Route::get('/result/{idcode}', [CandidateController::class, 'info']);
});

Route::post('/custom-broadcasting/auth-client', [CustomBroadcastController::class, 'authenticateClient']);
Route::post('/custom-broadcasting/auth-admin', [CustomBroadcastController::class, 'authenticateAdmin']);

Route::prefix('/room-status')->group(function () {
    // Lấy danh sách phòng thi
    Route::get('/rooms', [RoomStatusController::class, 'index']);

    // Lấy danh sách sinh viên trong phòng
    Route::get('/rooms/{roomId}/{subjectId}/students', [RoomStatusController::class, 'getStudents']);
});

Route::post('/candidate/{candidate}/finish', [CandidateController::class, 'finish']);
Route::get('/candidate/{candidate}/check-status', [CandidateController::class, 'checkExamStatus']);
Route::post('/candidate/{candidate}/update-status', [CandidateController::class, 'updateExamStatus']);
