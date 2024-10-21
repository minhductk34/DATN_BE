<?php

namespace App\Imports;

use App\Models\ExamSubject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class ExamSubjectImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new ExamSubject([
            'id' => $row['id'],
            'exam_id' => $row['exam_id'],
            'name' => $row['name'],
        ]);
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required|unique:exam_subjects,id',
            '*.exam_id' => 'required|exists:exams,id',
            '*.name' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.*.required' => ':attribute bắt buộc phải nhập',
            '*.id.unique' => ':attribute đã tồn tại',
            '*.exam_id.exists' => ':attribute không tồn tại',
            '*.name.string' => ':attribute phải là chuỗi',
            '*.name.max' => ':attribute tối đa :max kí tự',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'id' => 'Mã môn thi',
            'exam_id' => 'ID kì thi',
            'name' => 'Tên môn thi',
        ];
    }
}
