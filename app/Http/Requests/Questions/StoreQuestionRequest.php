<?php

namespace App\Http\Requests\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_content_id' => 'required|exists:exam_contents,id|max:255',
            'title' => 'required|string|max:255',
            'image_title' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            'answer_P' => 'required|string|max:255',
            'image_P' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',

            'answer_F1' => 'required|string|max:255',
            'image_F1' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            'answer_F2' => 'required|string|max:255',
            'image_F2' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            'answer_F3' => 'required|string|max:255',
            'image_F3' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',

            'level' => 'required|in:easy,medium,difficult',
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
            'in' => ':attribute không hợp lệ',
            'file' => ':attribute phải là một file',
            'mimes' => ':attribute không đúng định dạng ảnh cho phép (jpeg,jpg,png,gif,webp)'
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Mã câu hỏi',
            'exam_content_id' => 'Mã nội dung thi',
            'title' => 'Nội dung câu hỏi',
            'image_title' => 'Ảnh câu hỏi',
            'answer_P' => 'Đáp án đúng',
            'image_P' => 'Ảnh đáp án đúng',

            'answer_F1' => 'Đáp án sai 1',
            'image_F1' => 'Ảnh đáp án sai 1',
            'answer_F2' => 'Đáp án sai 2',
            'image_F2' => 'Ảnh đáp án sai 2',
            'answer_F3' => 'Đáp án sai 3',
            'image_F3' => 'Ảnh đáp án sai 3',

            'level' => 'Mức độ'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status' => '422',
            'data' => null,
            'warning' => $errors
        ], 422));
    }
}
