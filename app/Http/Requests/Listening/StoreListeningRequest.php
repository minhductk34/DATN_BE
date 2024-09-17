<?php

namespace App\Http\Requests\Listening;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreListeningRequest extends FormRequest
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
            'id' => 'required|unique:listenings,id',
            'exam_content_id' => 'required|exists:exam_contents,id',
            'Name' => 'required',
            'Audio' => 'required|file|mimes:audio/mpeg,mpga,mp3,wav|max:10240',
            'Status' => 'required|in:true,false',
            'Level' => 'nullable|in:Easy,Medium,Difficult',
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
            'mimes' => ':attribute không đúng định dạng',
            'max' => ':attribute quá lớn ( > 10 MB )',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Mã bài đọc',
            'exam_content_id' => 'ID nội dung thi',
            'Name' => 'Tên bài nghe',
            'Audio' => 'Bài nghe',
            'Status' => 'Trạng thái',
            'Level' => 'Độ khó',
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
