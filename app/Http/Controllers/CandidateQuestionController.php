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
        $questionCandidate = Candidate_question::query()->where('idCode', $validated['idCode'])->get();

        // Nếu có kết quả, trả về dữ liệu
        if ($questionCandidate->isNotEmpty()) {
            $point = Point::query()->where('exam_subject_id', '=', $validated['id_subject'])->where('idCode','=', $validated['idCode'])->first();
            
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
                        ->where('questions.id', $value->question_id)
                        ->orderByDesc('question_versions.version')  // Sắp xếp theo version giảm dần
                        ->select('question_versions.*', 'questions.exam_content_id')
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
                $finalResult = collect($finalResult)->groupBy('exam_content_id');

                foreach ($finalResult as $examContentId => $levels) {
                    foreach ($finalResult[$examContentId] as $key => $question) {
                        // Cập nhật thông tin kết quả
                        $result[$examContentId][$key]['id'] = $question['question_id'];
                        $result[$examContentId][$key]['title'] = $question['title'];
                        $result[$examContentId][$key]['image_title'] = $question['image_title'];

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
                            'id_pass' => $finalResult[$examContentId][$key]['id_pass']
                        ];
                    }
                }

                // Trả về kết quả đã nhóm
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
                        ->where('questions.exam_content_id', $requirement['exam_content_id'])
                        ->where('question_versions.level', $requirement['level'])
                        ->orderByDesc('question_versions.version')
                        ->limit($requirement['quantity'])
                        ->select('question_versions.*')
                        ->get();

                    $finalResult[$examContentId] = array_merge($finalResult[$examContentId], $questions->toArray());
                }

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate_question $candidate_question)
    {
        //
    }
}
