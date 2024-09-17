<?php

namespace App\Http\Requests\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'id' => 'required|max:255|unique:questions,id,' . $id,
            'exam_content_id' => 'required|exists:exam_contents,id|max:255',
            'Title' => 'required|string|max:255',
            'Image_Title' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            'Answer_P' => 'required|string|max:255',
            'Image_P' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',

            'Answer_F1' => 'required|string|max:255',
            'Image_F1' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            'Answer_F2' => 'required|string|max:255',
            'Image_F2' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            'Answer_F3' => 'required|string|max:255',
            'Image_F3' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',

            'Level' => 'required|in:Easy,Medium,Difficult',
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
            'Title' => 'Nội dung câu hỏi',
            'Image_Title' => 'Ảnh câu hỏi',
            'Answer_P' => 'Đáp án đúng',
            'Image_P' => 'Ảnh đáp án đúng',

            'Answer_F1' => 'Đáp án sai 1',
            'Image_F1' => 'Ảnh đáp án sai 1',
            'Answer_F2' => 'Đáp án sai 2',
            'Image_F2' => 'Ảnh đáp án sai 2',
            'Answer_F3' => 'Đáp án sai 3',
            'Image_F3' => 'Ảnh đáp án sai 3',

            'Level' => 'Mức độ'
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
