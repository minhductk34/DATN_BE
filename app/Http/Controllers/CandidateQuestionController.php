<?php

namespace App\Http\Controllers;

use App\Models\Candidate_question;
use App\Models\CandidateQuestion;
use App\Models\Exam_structure;
use App\Models\Exam_subject;
use App\Models\Exam_subject_detail;
use App\Models\Point;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CandidateQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function exam(Request $request)
    {
        $validated = $request->validate([
            'id_subject' => 'required|exists:exam_subjects,id',
            'idCode' => 'required|exists:candidates,idcode'
        ]);

        $exam_subject_detail = Exam_subject_detail::query()->where('exam_subject_id', '=', $validated['id_subject'])->first();

        // Kiểm tra xem câu hỏi đã tồn tại trong bảng Candidate_question chưa
        $questionCandidate = Candidate_question::query()->where('subject_id', $validated['id_subject'])->where('idCode', $validated['idCode'])->get();

        // Nếu có kết quả, trả về dữ liệu
        if ($questionCandidate->isNotEmpty()) {
            $point = Point::query()->where('exam_subject_id', '=', $validated['id_subject'])->where('idCode', '=', $validated['idCode'])->first();

            if ($point) {
                return response()->json([
                    'message' => 'Exam successfully',
                    'data' => $point,
                ], 500);
            } else {
                $result = [];

                foreach ($questionCandidate as $key => $value) {
                    // Lấy thông tin câu hỏi với version mới nhất
                    $questions = Question::query()
                        ->join('question_versions', 'questions.id', '=', 'question_versions.question_id')
                        ->join('exam_contents', 'exam_contents.id', '=', 'questions.exam_content_id')
                        ->where('questions.id', $value->question_id)
                        ->orderByDesc('question_versions.version')  // Sắp xếp theo version giảm dần
                        ->select('question_versions.*', 'questions.exam_content_id', 'exam_contents.url_listening', 'exam_contents.description')
                        ->first();  // Lấy câu hỏi đầu tiên (mới nhất)

                    $mapping = [
                        'A' => 1,
                        'B' => 2,
                        'C' => 3,
                        'D' => 4,
                    ];

                    $questions->stemp = $mapping[$value->answer_Temp] ?? null;
                    $questions->id_pass = $mapping[$value->answer_P];
                    // Thêm câu hỏi vào mảng finalResult
                    $finalResult[] = $questions;
                }

                // Chuyển mảng finalResult thành collection và nhóm theo exam_content_id
                $finalResult = collect($finalResult)->groupBy(groupBy: 'exam_content_id');

                foreach ($finalResult as $examContentId => $levels) {
                    foreach ($finalResult[$examContentId] as $key => $question) {
                        // Cập nhật thông tin kết quả
                        $result[$examContentId][$key]['id'] = $question['question_id'];
                        $result[$examContentId][$key]['title'] = $question['title'];
                        $result[$examContentId][$key]['image_title'] = $question['image_title'];
                        $result[$examContentId][$key]['examContentId'] = $examContentId;
                        $result[$examContentId][$key]['url_listening'] = $finalResult[$examContentId][$key]['url_listening'];
                        $result[$examContentId][$key]['description'] = $finalResult[$examContentId][$key]['description'];

                        $result[$examContentId][$key]['answer'] = [
                            'temp' =>  $finalResult[$examContentId][$key]['stemp'],
                            'correct' => $question['answer_P'],
                            'img_correct' => $question['image_P'],
                            'wrong1' => $question['answer_F1'],
                            'img_wrong1' => $question['image_F1'],
                            'wrong2' => $question['answer_F2'],
                            'img_wrong2' => $question['image_F2'],
                            'wrong3' => $question['answer_F3'],
                            'img_wrong3' => $question['image_F3'],
                            'id_pass' => $finalResult[$examContentId][$key]['id_pass'],
                        ];
                    }
                }
                $updated = DB::table('time_exam_cadidate')
                ->where('idcode', $validated['idCode'])
                ->where('subject_id',  $validated['id_subject'])
                ->first();
    
                // Trả về kết quả đã nhóm
                $data = [
                    'time' => $updated->time,
                    'question' => $result,
                ];


                // Trả về dữ liệu ứng viên đã được xử lý
                return response()->json([
                    'message' => 'Featch question successfully',
                    'data' => $data,
                ], 200);
            }
        } else {

            // Lấy cấu trúc bài thi
            $exam_structure = Exam_structure::query()->where('exam_subject_id', '=', $validated['id_subject'])->get();

            // Nhóm câu hỏi theo exam_content_id
            $groupedByLever = $exam_structure->groupBy('exam_content_id');

            // Khởi tạo các mảng để lưu kết quả
            $finalResult = [];
            $result = [];
            $candidate = [];
            // Xử lý từng nhóm câu hỏi theo exam_content_id
            foreach ($groupedByLever as $examContentId => $levels) {
                $finalResult[$examContentId] = [];

                // Lấy danh sách câu hỏi cho từng yêu cầu
                foreach ($levels as $requirement) {
                    $questions = Question::query()
                        ->join('question_versions', 'questions.id', '=', 'question_versions.question_id')
                        ->join('exam_contents','exam_contents.id', '=','questions.exam_content_id')
                        ->where('questions.exam_content_id', $requirement['exam_content_id'])
                        ->orderByDesc('question_versions.version')
                        ->limit($requirement['quantity'])
                        ->select('question_versions.*','exam_contents.url_listening','exam_contents.description' )
                        ->get();

                    $finalResult[$examContentId] = array_merge($finalResult[$examContentId], $questions->toArray());
                }
                Log::error('Export Excel Error:', [
                    'message' => $finalResult[$examContentId],
                ]);
            
                // Xử lý câu hỏi cho từng câu hỏi trong finalResult
                foreach ($finalResult[$examContentId] as $key => $question) {
                    $finalResult[$examContentId][$key]['id_pass'] = rand(1, 4);

                    // Cập nhật câu trả lời của ứng viên
                    $candidate[] = [
                        'question_id' => $question['question_id'],
                        'numerical_order' => $question['version'],
                        'answer_P' => $finalResult[$examContentId][$key]['id_pass'] == 1 ? "A" : ($finalResult[$examContentId][$key]['id_pass'] == 2 ? "B" : ($finalResult[$examContentId][$key]['id_pass'] == 3 ? "C" : "D")),
                    ];

                    // Cập nhật thông tin kết quả
                    $result[$examContentId][$key]['id'] = $question['question_id'];
                    $result[$examContentId][$key]['title'] = $question['title'];
                    $result[$examContentId][$key]['image_title'] = $question['image_title'];
                    $result[$examContentId][$key]['examContentId'] = $examContentId;
                    $result[$examContentId][$key]['url_listening'] = $finalResult[$examContentId][$key]['url_listening'];
                    $result[$examContentId][$key]['description'] = $finalResult[$examContentId][$key]['description'];

                    // Lưu đáp án theo mức độ id_pass
                    if ($finalResult[$examContentId][$key]['id_pass'] == 1) {
                        $result[$examContentId][$key]['answer'] = [
                            'correct' => $question['answer_P'],
                            'img_correct' => $question['image_P'],
                            'wrong1' => $question['answer_F1'],
                            'img_wrong1' => $question['image_F1'],
                            'wrong2' => $question['answer_F2'],
                            'img_wrong2' => $question['image_F2'],
                            'wrong3' => $question['answer_F3'],
                            'img_wrong3' => $question['image_F3'],
                        ];
                    } elseif ($finalResult[$examContentId][$key]['id_pass'] == 2) {
                        $result[$examContentId][$key]['answer'] = [
                            'wrong1' => $question['answer_F1'],
                            'img_wrong1' => $question['image_F1'],
                            'correct' => $question['answer_P'],
                            'img_correct' => $question['image_P'],
                            'wrong2' => $question['answer_F2'],
                            'img_wrong2' => $question['image_F2'],
                            'wrong3' => $question['answer_F3'],
                            'img_wrong3' => $question['image_F3'],
                        ];
                    } elseif ($finalResult[$examContentId][$key]['id_pass'] == 3) {
                        $result[$examContentId][$key]['answer'] = [
                            'wrong1' => $question['answer_F1'],
                            'img_wrong1' => $question['image_F1'],
                            'wrong2' => $question['answer_F2'],
                            'img_wrong2' => $question['image_F2'],
                            'correct' => $question['answer_P'],
                            'img_correct' => $question['image_P'],
                            'wrong3' => $question['answer_F3'],
                            'img_wrong3' => $question['image_F3'],
                        ];
                    } else {
                        $result[$examContentId][$key]['answer'] = [
                            'wrong1' => $question['answer_F1'],
                            'img_wrong1' => $question['image_F1'],
                            'wrong2' => $question['answer_F2'],
                            'img_wrong2' => $question['image_F2'],
                            'wrong3' => $question['answer_F3'],
                            'img_wrong3' => $question['image_F3'],
                            'correct' => $question['answer_P'],
                            'img_correct' => $question['image_P'],
                        ];
                    }
                }
            }

            // Lưu dữ liệu của ứng viên vào bảng Candidate_question
            foreach ($candidate as $key => $value) {
                Candidate_question::create([
                    'question_id' => $value['question_id'],
                    'idcode' => $validated['idCode'],
                    'subject_id' => $validated['id_subject'],
                    'numerical_order' => $value['numerical_order'],
                    'answer_P' => $value['answer_P'],
                ]);
            }

            DB::table('time_exam_cadidate')->insert([
                'idcode' => $validated['idCode'],
                'subject_id' => $validated['id_subject'],
                'time' => $exam_subject_detail->time * 60,
            ]);

            $data = [
                'time' => $exam_subject_detail->time,
                'question' => $result,
            ];



            // Trả về dữ liệu ứng viên đã được xử lý
            return response()->json([
                'message' => 'Featch question successfully',
                'data' => $data,
            ], 200);
        }
    }

    public function update_time(Request $request)
    {
        $validated = $request->validate([
            'id_subject' => 'required',
            'idcode' => 'required',
            'time' => 'required'
        ]);

        // Cập nhật bản ghi theo idcode và subject_id
        $updated = DB::table('time_exam_cadidate')
            ->where('idcode', $validated['idcode'])
            ->where('subject_id',  $validated['id_subject'])
            ->update(['time' =>  $validated['time']]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Time exam candidate updated successfully.',
                
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Time exam candidate not found.',
            'dataa'=>[$validated['idcode'],$validated['id_subject'],$validated['time']]
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate_question $candidate_question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Candidate_question $candidate_question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,)
    {
        $validated = $request->validate([
            'id_question' => 'required',
            'idCode' => 'required|exists:candidates,idcode',
            'subject_id' => 'required',
            'temp' => 'required|integer|between:1,4'
        ]);

        $tempMapping = [
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D',
        ];

        $exam_subject_detail = Candidate_question::query()
            ->where('question_id', $validated['id_question'])
            ->where('idcode', $validated['idCode'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        if ($exam_subject_detail) {
            $exam_subject_detail->update([
                'answer_Temp' => $tempMapping[$validated['temp']],
            ]);

            return response()->json(['message' => 'Update successful', 'success' => true], 200);
        }

        return response()->json(['message' => 'Record not found', 'success' => false], 404);
    }

    public function finish(Request $request)
    {
        $validated = $request->validate([

            'idCode' => 'required|exists:candidates,idcode',
            'subject_id' => 'required',

        ]);

        $exam_subject_detail = Candidate_question::query()
            ->where('idcode', $validated['idCode'])
            ->where('subject_id', $validated['subject_id'])
            ->get();

        if ($exam_subject_detail) {
            $total = 0;
            $pass = 0;
            $point = 0;

            foreach ($exam_subject_detail as $key => $value) {
                if ($value->answer_P == $value->answer_Temp) {
                    $pass += 1;
                }

                $total += 1;
            }

            $data = Point::create([
                'exam_subject_id' => $validated['subject_id'],
                'idcode' => $validated['idCode'],
                'point' => ($pass / $total) * 10,
                'number_of_correct_sentences' => $pass,
            ]);

            return response()->json(['message' => 'Update successful', 'data' => $data, 'success' => true], 200);
        }

        return response()->json(['message' => 'Record not found', 'success' => false], 404);
    }

    public function scoreboard($id)
    {
        $results = DB::table('points')
            ->join('exam_subjects', 'exam_subjects.id', '=', 'points.exam_subject_id')
            ->join('exams', 'exams.id', '=', 'exam_subjects.exam_id')
            ->where('points.exam_id', $id) // Bổ sung điều kiện `WHERE` theo `$id` được truyền vào
            ->select(
                'exams.id as exam_id',
                'exams.name as exam_name',
                'exam_subjects.id as subject_id',
                'exam_subjects.name as subject_name',
                'points.point',
                DB::raw('CASE WHEN points.point >= 5 THEN "Đạt" ELSE "Không đạt" END as status')
            )
            ->get();

        return response()->json($results);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate_question $candidate_question)
    {
        //
    }
}
