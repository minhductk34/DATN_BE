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
    use Importable, SkipsFailures;

    protected $importedIds = []; // Lưu trữ các ID đã thêm mới

    public function model(array $row)
    {
        $examContent = new Exam_content([
            // 'id' => $row['id'],
            'exam_subject_id' => $row['exam_subject_id'],
            'title' => $row['title'],
        ]);

        // Lưu ID của bản ghi đã thêm
        $this->importedIds[] = $examContent;

        return $examContent;
    }

    public function rules(): array
    {
        return [
            '*.exam_subject_id' => 'required|exists:exam_subjects,id',
            '*.title' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.required' => ':attribute bắt buộc phải nhập.',
            '*.exists' => ':attribute không tồn tại trong cơ sở dữ liệu.',
            '*.unique' => ':attribute đã tồn tại trong cơ sở dữ liệu cho môn thi này.',
            '*.string' => ':attribute phải là chuỗi.',
            '*.max' => ':attribute tối đa :max ký tự.',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '*.exam_subject_id' => 'Mã môn thi',
            '*.title' => 'Nội dung thi',
        ];
    }

    private function excelDateToPhpDate($excelDate)
    {
        if (!is_numeric($excelDate)) {
            return $excelDate;
        }

        $unixTimestamp = ($excelDate - 25569) * 86400;
        return Carbon::createFromTimestamp($unixTimestamp)->format('Y-m-d H:i:s');
    }

    public function getImportedData()
    {
        return  $this->importedIds;
    }
}
