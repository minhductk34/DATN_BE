<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportExelRequest extends FormRequest
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
            'file' => 'required|file|mimes:xlsx,xls'
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Hãy chọn một file để tải lên',
            'file.file' => 'Hãy chọn một file để tải lên',
            'file.mimes' => 'File không đúng định dạng ( .xlsx, .xls )',
        ];
    }
}
