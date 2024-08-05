<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExcleExamContent extends FormRequest
{
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
            // 'id' => 'required|unique:exam_contents,id',
            'exam_subject_id' => 'required|exists:exam_subjects,id',
            'title' => 'required|string|max:255',
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

        ];
    }

    public function attributes()
    {
        return [
            // 'id' => 'Mã nội dung thi',
            'exam_subject_id' => 'Mã môn thi',
            'title' => 'Nội dung thi',
        ];
    }
}
