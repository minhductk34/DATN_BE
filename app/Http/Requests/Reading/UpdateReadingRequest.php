<?php

namespace App\Http\Requests\Reading;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateReadingRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'id' => 'required|unique:readings,id,' . $id,
            'exam_content_id' => 'required|exists:exam_contents,id',
            'title' => 'required',
            'status' => 'required|in:true,false',
            'level' => 'nullable|in:easy,medium,difficult',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute bắt buộc phải nhập',
            'unique' => ':attribute đã tồn tại',
            'exists' => ':attribute không tồn tại',
            'in' => ':attribute không hợp lệ',
            'file' => ':attribute phải là một file',
            'mimes' => ':attribute không đúng định dạng (jpeg,jpg,png,gif,webp)'
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Mã bài đọc',
            'exam_content_id' => 'iD nội dung thi',
            'title' => 'Bài đọc',
            'status' => 'Trạng thái',
            'level' => 'Độ khó',
            'image' => 'Ảnh'
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
