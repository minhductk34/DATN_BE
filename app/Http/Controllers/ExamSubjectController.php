<?php

namespace App\Http\Controllers;

use App\Models\Exam_subject as ExamSubject;
use Illuminate\Http\Request;

class ExamSubjectController extends Controller
{
    /**
     * Lấy môn thi theo kì thi
     */
    public function getSubjectByExam($id)
    {
        $examSubjects = ExamSubject::query()->where('exam_id', $id)->get();

        return response()->json(
            [
                'data' => $examSubjects,
                'status' => 'success'
            ],200);
    }

    /**
     * Thêm môn thi
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(
            [
                'id' => 'required|unique:exam_subjects,id',
                'exam_id' => 'required|exists:exams,id',
                'Name' => 'required|string|max:255',
                'Status' => 'required|in:true,false',
                'TimeStart' => 'required|date',
                'TimeEnd' => 'required|date|after:TimeStart',
            ],
            [
                'required' => ':attribute bắt buộc phải nhập',
                'unique' => ':attribute đã tồn tại',
                'exists' => ':attribute không tồn tại',
                'string' => ':attribute phải là chuỗi',
                'max' => ':attribute tối đa :max kí tự',
                'in' => 'Trạng thái không hợp lệ',
                'date' => ':attribute không đúng định dạng',
                'after' => 'Thời gian kết thúc phải sau thời gian bắt đầu'
            ],
            [
                'id' => 'Mã môn thi',
                'exam_id' => 'ID kì thi',
                'Name' => 'Tên môn thi',
                'Status' => 'Trạng thái',
                'TimeStart' => 'Thời gian bắt đầu',
                'TimeEnd' => 'Thời gian kết thúc',
            ]
        );

        $examSubject = ExamSubject::create($validatedData);
        return response()->json(
            [
                'data' => $examSubject,
                'status' => 'success'
            ],201);
    }

    /**
     * Hiển thị thông tin chi tiết một môn thi
     */
    public function show($id)
    {
        $examSubject = ExamSubject::with('contents')->find($id);

        if (!$examSubject) {
            return response()->json(['message' => 'Môn thi không tồn tại'], 404);
        }

        return response()->json($examSubject, 200);
    }

    /**
     * Cập nhật thông tin môn thi
     */
    public function update(Request $request, $id)
    {
        $examSubject = ExamSubject::find($id);

        if (!$examSubject) {
            return response()->json(['message' => 'Môn thi không tồn tại'], 404);
        }

        $validatedData = $request->validate(
            [
                'id' => 'required|unique:exam_subjects,id,' . $id,
                'exam_id' => 'required|exists:exams,id',
                'Name' => 'required|string|max:255',
                'Status' => 'required|in:true,false',
                'TimeStart' => 'required|date',
                'TimeEnd' => 'required|date|after:TimeStart',
            ],
            [
                'required' => ':attribute bắt buộc phải nhập',
                'unique' => ':attribute đã tồn tại',
                'exists' => ':attribute không tồn tại',
                'string' => ':attribute phải là chuỗi',
                'max' => ':attribute tối đa :max kí tự',
                'in' => 'Trạng thái không hợp lệ',
                'date' => ':attribute không đúng định dạng',
                'after' => 'Thời gian kết thúc phải sau thời gian bắt đầu'
            ],
            [
                'id' => 'Mã môn thi',
                'exam_id' => 'ID kì thi',
                'Name' => 'Tên môn thi',
                'Status' => 'Trạng thái',
                'TimeStart' => 'Thời gian bắt đầu',
                'TimeEnd' => 'Thời gian kết thúc',
            ]
        );

        $examSubject->update($validatedData);
        return response()->json(
            [
                'data' => $examSubject,
                'status' => 'success'
            ], 200);
    }

    /**
     * Xóa môn thi ( xóa mềm )
     */
    public function destroy($id)
    {
        $examSubject = ExamSubject::query()->find($id);

        if (!$examSubject) {
            return response()->json(['message' => 'Môn thi không tồn tại'], 404);
        }

        $examSubject->delete();
        return response()->json([], 204);
    }

    /**
     * Khôi phục môn thi bị xóa
     */
    public function restore($id)
    {
        ExamSubject::withTrashed()->where('id', $id)->restore();

        return response()->json([], 204);
    }
}
