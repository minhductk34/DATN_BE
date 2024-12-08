<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $question = Question::create([
            'id' => $row['ma_cau_hoi'],
            'exam_content_id' => $row['ma_noi_dung'],
            'status' => true
        ]);

        $version = $question->version()->create([
            'title' => $row['noi_dung_cau_hoi'],
            'question_id' => $question->id,
            'level' => $row['muc_do'],
            'answer_P' => $row['dap_an_dung'],
            'answer_F1' => $row['dap_an_sai_1'],
            'answer_F2' => $row['dap_an_sai_2'],
            'answer_F3' => $row['dap_an_sai_3'],
            'version' => 1,
            'is_active' => true
        ]);

        $question->update([
            'current_version_id' => $version->id
        ]);

        return $question;
    }

    public function rules(): array
    {
        return [
            '*.ma_cau_hoi' => ['required', 'unique:questions,id'],
            '*.ma_noi_dung' => 'required|exists:exam_contents,id',
            '*.noi_dung_cau_hoi' => 'required',
            '*.muc_do' => 'required|in:easy,medium,hard',
            '*.dap_an_dung' => 'required',
            '*.dap_an_sai_1' => 'required',
            '*.dap_an_sai_2' => 'required',
            '*.dap_an_sai_3' => 'required'
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.ma_cau_hoi.required' => 'Mã câu hỏi không được để trống',
            '*.ma_cau_hoi.unique' => 'Mã câu hỏi :input đã tồn tại trong hệ thống',
            '*.ma_noi_dung.required' => 'Mã nội dung không được để trống',
            '*.ma_noi_dung.exists' => 'Mã nội dung không tồn tại trong hệ thống',
            '*.noi_dung_cau_hoi.required' => 'Nội dung câu hỏi không được để trống',
            '*.muc_do.required' => 'Mức độ không được để trống',
            '*.muc_do.in' => 'Mức độ phải là: easy, medium hoặc hard',
            '*.dap_an_dung.required' => 'Đáp án đúng không được để trống',
            '*.dap_an_sai_1.required' => 'Đáp án sai 1 không được để trống',
            '*.dap_an_sai_2.required' => 'Đáp án sai 2 không được để trống',
            '*.dap_an_sai_3.required' => 'Đáp án sai 3 không được để trống'
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}
