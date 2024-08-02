<?php

namespace App\Http\Requests\ExamSubject;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamSubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|unique:exam_subjects,id',
            'exam_id' => 'required|exists:exams,id',
            'Name' => 'required|string|max:255',
            'Status' => 'required|in:true,false',
            'TimeStart' => 'required|date',
            'TimeEnd' => 'required|date|after:TimeStart',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute bắt buộc phải nhập',
            'unique' => ':attribute đã tồn tại',
            'exists' => ':attribute không tồn tại',
            'string' => ':attribute phải là chuỗi',
            'max' => ':attribute tối đa :max kí tự',
            'in' => 'Trạng thái không hợp lệ',
            'date' => ':attribute không đúng định dạng',
            'after' => 'Thời gian kết thúc phải sau thời gian bắt đầu'
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Mã môn thi',
            'exam_id' => 'ID kì thi',
            'Name' => 'Tên môn thi',
            'Status' => 'Trạng thái',
            'TimeStart' => 'Thời gian bắt đầu',
            'TimeEnd' => 'Thời gian kết thúc',
        ];
    }
}
