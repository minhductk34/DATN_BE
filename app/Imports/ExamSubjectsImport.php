<?php
namespace App\Imports;

use App\Models\Exam_subject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ExamSubjectsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Exam_subject([
            'id' => $row['ma_mon_thi'],
            'name' => $row['ten_mon_thi'],
            'exam_id' => $row['ma_ky_thi'],
            'status' => true
        ]);
    }

    public function rules(): array {
        return [
            'ma_mon_thi' => 'required|unique:exam_subjects,id',
            'ten_mon_thi' => 'required',
            'ma_ky_thi' => 'required|exists:exams,id'
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
    public function customValidationMessages()
    {
        return [
            'ma_mon_thi.required' => 'Mã môn thi không được để trống',
            'ma_mon_thi.unique' => 'Mã môn thi đã tồn tại trong hệ thống',
            'ten_mon_thi.required' => 'Tên môn thi không được để trống',
            'ma_ky_thi.required' => 'Mã kỳ thi không được để trống',
            'ma_ky_thi.exists' => 'Mã kỳ thi không tồn tại trong hệ thống'
        ];
    }
}
