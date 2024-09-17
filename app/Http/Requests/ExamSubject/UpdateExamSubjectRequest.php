<?php

namespace App\Http\Requests\ExamSubject;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateExamSubjectRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'id' => 'required|unique:exam_subjects,id,' . $id,
            'exam_id' => 'required|exists:exams,id',
            'Name' => 'required|string|max:255',
            'Status' => 'required|in:true,false',
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
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Mã môn thi',
            'exam_id' => 'ID kì thi',
            'Name' => 'Tên môn thi',
            'Status' => 'Trạng thái',
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
