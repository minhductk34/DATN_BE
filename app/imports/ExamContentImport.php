<?php

namespace App\Imports;

use App\Models\Exam_content;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ExamContentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Exam_content([
            'id' => $row['id'],
            'exam_subject_id' => $row['exam_subject_id'],
            'title' => $row['title'],
        ]);
    }

    // public function prepareForValidation(array $row)
    // {
    //     $row['status'] = $row['status'] == 0 ? 'false' : 'true';
    //     $row['time_start'] = $this->excelDateToPhpDate($row['time_start']);
    //     $row['time_end'] = $this->excelDateToPhpDate($row['time_end']);

    //     return $row;
    // }

    public function rules(): array
    {
        return [
            // '*.id' => 'required|exists:exam_contents,id',
            '*.exam_subject_id' => 'required|unique:exam_subjects,id',
            '*.title' => 'required|string|max:255',
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
            '*.date' => ':attribute không đúng định dạng',
            '*.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu'
        ];
    }

    public function customValidationAttributes()
    {
        return [
            // '*.id' => 'Mã nội dung thi',
            '*.exam_subject_id' => 'Mã môn thi',
            '*.title' => 'Nội dung thi',
        ];
    }

    private function excelDateToPhpDate($excelDate)
    {
        if (!is_numeric($excelDate)) {
            return $excelDate;
        }

        // Convert Excel date to PHP date format
        $unixTimestamp = ($excelDate - 25569) * 86400;
        return Carbon::createFromTimestamp($unixTimestamp)->format('Y-m-d H:i:s');
    }
}
