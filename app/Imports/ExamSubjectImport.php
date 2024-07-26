<?php

namespace App\Imports;

use App\Models\Exam_subject;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class ExamSubjectImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Exam_subject([
            'id' => $row['id'],
            'exam_id' => $row['exam_id'],
            'Name' => $row['name'],
            'Status' => $row['status'],
            'TimeStart' => $row['time_start'],
            'TimeEnd' => $row['time_end'],
        ]);
    }

    public function prepareForValidation(array $row)
    {
        $row['status'] = $row['status'] == 0 ? 'false' : 'true';
        $row['time_start'] = $this->excelDateToPhpDate($row['time_start']);
        $row['time_end'] = $this->excelDateToPhpDate($row['time_end']);
        
        return $row;
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required|unique:exam_subjects,id',
            '*.exam_id' => 'required|exists:exams,id',
            '*.name' => 'required|string|max:255',
            '*.status' => 'required',
            '*.time_start' => 'required|date',
            '*.time_end' => 'required|date|after:*.time_start',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.required' => ':attribute bắt buộc phải nhập',
            '*.unique' => ':attribute đã tồn tại',
            '*.exists' => ':attribute không tồn tại',
            '*.string' => ':attribute phải là chuỗi',
            '*.max' => ':attribute tối đa :max kí tự',
            '*.in' => 'Trạng thái không hợp lệ',
            '*.date' => ':attribute không đúng định dạng',
            '*.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu'
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '*.id' => 'Mã môn thi',
            '*.exam_id' => 'ID kì thi',
            '*.name' => 'Tên môn thi',
            '*.status' => 'Trạng thái',
            '*.time_start' => 'Thời gian bắt đầu',
            '*.time_end' => 'Thời gian kết thúc',
        ];
    }

    private function excelDateToPhpDate($excelDate)
    {
        if (!is_numeric($excelDate)) {
            return $excelDate;
        }

        // Excel sử dụng hệ thống ngày bắt đầu từ 1/1/1900
        $unixTimestamp = ($excelDate - 25569) * 86400;
        return Carbon::createFromTimestamp($unixTimestamp)->timezone('UTC')->format('Y-m-d H:i:s');
    }
}
