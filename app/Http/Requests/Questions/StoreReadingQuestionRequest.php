<?php

namespace App\Http\Requests\Questions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReadingQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|unique:reading_questions,id|max:255',
            'reading_id' => 'required|exists:readings,id|max:255',
            'Title' => 'required|string|max:255',
            'Answer_P' => 'required|string|max:255',
            'Answer_F1' => 'required|string|max:255',
            'Answer_F2' => 'required|string|max:255',
            'Answer_F3' => 'required|string|max:255',
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
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Mã câu hỏi',
            'reading_id' => 'Mã bài đọc',
            'Title' => 'Nội dung câu hỏi',
            'Answer_P' => 'Đáp án đúng',

            'Answer_F1' => 'Đáp án sai 1',
            'Answer_F2' => 'Đáp án sai 2',
            'Answer_F3' => 'Đáp án sai 3',

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
