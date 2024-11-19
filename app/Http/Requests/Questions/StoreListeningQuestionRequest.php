<?php

namespace App\Http\Requests\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreListeningQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|unique:listening_questions,id|max:255',
            'listening_id' => 'required|exists:listenings,id|max:255',
            'title' => 'required|string|max:255',
            'answer_P' => 'required|string|max:255',
            'answer_F1' => 'required|string|max:255',
            'answer_F2' => 'required|string|max:255',
            'answer_F3' => 'required|string|max:255',
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
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Mã câu hỏi',
            'listening_id' => 'Mã bài nghe',
            'title' => 'Nội dung câu hỏi',
            'answer_P' => 'Đáp án đúng',

            'answer_F1' => 'Đáp án sai 1',
            'answer_F2' => 'Đáp án sai 2',
            'answer_F3' => 'Đáp án sai 3',

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
