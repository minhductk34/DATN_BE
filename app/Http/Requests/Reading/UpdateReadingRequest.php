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
            'Title' => 'required',
            'Status' => 'required|in:true,false',
            'Level' => 'nullable|in:Easy,Medium,Difficult',
            'Image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
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
            'exam_content_id' => 'ID nội dung thi',
            'Title' => 'Bài đọc',
            'Status' => 'Trạng thái',
            'Level' => 'Độ khó',
            'Image' => 'Ảnh'
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
