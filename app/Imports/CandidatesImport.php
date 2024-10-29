<?php

namespace App\Imports;

use App\Models\Candidate;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CandidatesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * Ánh xạ dữ liệu từ mỗi hàng trong file Excel vào Model Candidate.
     */
    public function model(array $row)
    {
        return new Candidate([
            'Idcode' => $row['idcode'],
            'exam_id' => $row['exam_id'],
            'Fullname' => $row['fullname'],
            'Image' => $row['image'],
            'DOB' => $row['dob'],
            'Address' => $row['address'],
            'Examination_room' => $row['examination_room'],
            'Password' => bcrypt($row['password']),
            'Email' => $row['email'],
            'Status' => $row['status']
        ]);
    }

    /**
     * Cấu hình các quy tắc validate cho từng hàng dữ liệu.
     */
    public function rules(): array
    {
        return [
            '*.idcode' => 'required|string|max:255',
            '*.exam_id' => 'required|string|max:255',
            '*.fullname' => 'required|string|max:255',
            '*.image' => 'nullable|string|max:255',
            '*.dob' => 'required|date',
            '*.address' => 'required|string|max:255',
            '*.examination_room' => 'required|string|max:255',
            '*.password' => 'required|string|min:6',
            '*.email' => [
                'required',
                'email',
                Rule::unique('candidates', 'email')
            ],
            '*.status' => 'required|string|max:255'
        ];
    }

    /**
     * Tùy chọn, có thể cấu hình thêm thông báo lỗi tiếng Việt.
     */
    public function customValidationMessages()
    {
        return [
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            '*.required' => ':attribute là bắt buộc.',
            '*.email' => ':attribute phải là một địa chỉ email hợp lệ.',
            '*.max' => ':attribute không được vượt quá :max ký tự.',
            '*.min' => ':attribute phải có ít nhất :min ký tự.',
            '*.date' => ':attribute phải là một ngày hợp lệ.'
        ];
    }
}
