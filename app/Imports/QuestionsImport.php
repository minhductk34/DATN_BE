<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ImportQuestions implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $question = Question::create([
            'id' => $row['ma_cau_hoi'],
            'exam_content_id' => $row['ma_noi_dung'],
            'title' => $row['noi_dung_cau_hoi'],
            'level' => $row['muc_do'],
            'answer_P' => $row['dap_an_dung'],
            'answer_F1' => $row['dap_an_sai_1'],
            'answer_F2' => $row['dap_an_sai_2'],
            'answer_F3' => $row['dap_an_sai_3'],
            'status' => true
        ]);

        // Tạo version cho câu hỏi mới
        $version = $question->version()->create([
            'title' => $row['noi_dung_cau_hoi'],
            'question_id' => $question->id,
            'answer_P' => $row['dap_an_dung'],
            'answer_F1' => $row['dap_an_sai_1'],
            'answer_F2' => $row['dap_an_sai_2'],
            'answer_F3' => $row['dap_an_sai_3'],
            'level' => $row['muc_do'],
            'version' => 1,
            'is_active' => true
        ]);

        $question->update(['current_version_id' => $version->id]);

        return $question;
    }

    public function rules(): array
    {
        return [
            'ma_cau_hoi' => 'required|unique:questions,id',
            'ma_noi_dung' => 'required|exists:exam_contents,id',
            'noi_dung_cau_hoi' => 'required',
            'muc_do' => 'required|in:easy,medium,difficult',
            'dap_an_dung' => 'required',
            'dap_an_sai_1' => 'required',
            'dap_an_sai_2' => 'required',
            'dap_an_sai_3' => 'required'
        ];
    }
}
