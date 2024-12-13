<?php

namespace App\Imports;

use App\Models\Exam_content;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Importable;

class ExamContentImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use SkipsFailures, Importable;

    private $rowCount = 0;
    protected $failures = [];

    public function model(array $row)
    {
        $this->rowCount++;

        try {
            return new Exam_content([
                'id' => trim($row['ma_noi_dung']),
                'exam_subject_id' => trim($row['ma_mon']),
                'title' => trim($row['ten']),
                'url_listening' => isset($row['url_bai_nghe']) ? trim($row['url_bai_nghe']) : null,
                'description' => isset($row['noi_dung']) ? trim($row['noi_dung']) : null,
                'status' => true
            ]);
        } catch (\Exception $e) {
            $this->failures[] = [
                'row' => $this->rowCount,
                'error' => $e->getMessage()
            ];
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.ma_noi_dung' => [
                'required',
                'string',
                'max:255',
                'regex:/^(BD_|BN_|NP_)/',
                'unique:exam_contents,id'
            ],
            '*.ma_mon' => [
                'required',
                'string',
                'exists:exam_subjects,id'
            ],
            '*.ten' => [
                'required',
                'string'
            ],
            '*.url_bai_nghe' => [
                'nullable',
                'string'
            ],
            '*.noi_dung' => [
                'nullable',
                'string'
            ]
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.ma_noi_dung.required' => 'Mã nội dung thi không được để trống',
            '*.ma_noi_dung.string' => 'Mã nội dung thi phải là chuỗi ký tự',
            '*.ma_noi_dung.max' => 'Mã nội dung thi không được vượt quá :max ký tự',
            '*.ma_noi_dung.regex' => 'Mã nội dung thi phải bắt đầu bằng BD_, BN_ hoặc NP_',
            '*.ma_noi_dung.unique' => 'Mã nội dung thi đã tồn tại',

            '*.ma_mon.required' => 'Mã môn thi không được để trống',
            '*.ma_mon.string' => 'Mã môn thi phải là chuỗi ký tự',
            '*.ma_mon.exists' => 'Mã môn thi không tồn tại trong hệ thống',

            '*.ten.required' => 'Tên nội dung thi không được để trống',
            '*.ten.string' => 'Tên nội dung thi phải là chuỗi ký tự',

            '*.url_bai_nghe.string' => 'URL bài nghe phải là chuỗi ký tự',

            '*.noi_dung.string' => 'Nội dung bài đọc phải là chuỗi ký tự'
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getFailures(): array
    {
        $failureCount = count($this->failures);

        if ($failureCount === 0) {
            return [];
        }

        $firstFailure = $this->failures[0];
        $row = $firstFailure->row();
        $errors = $firstFailure->errors();

        $errorMessage = is_array($errors) ? implode(', ', $errors) : (string) $errors;

        if ($failureCount === 1) {
            return ["Dòng {$row}: {$errorMessage}"];
        } else {
            $number = $failureCount -1;
            return ["Dòng {$row}: {$errorMessage} và {$number} lỗi khác"];
        }
    }



    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
