<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $exam_content_id;

    public function __construct($exam_content_id)
    {
        $this->exam_content_id = $exam_content_id;
    }

    public function model(array $row)
    {
        // Tạo UUID cho ID câu hỏi
        $questionId = (string) Str::uuid();

        $question = Question::create([
            'id' => $questionId,
            'exam_content_id' => $this->exam_content_id,
            'status' => true
        ]);

        $version = $question->version()->create([
            'title' => $row['noi_dung_cau_hoi'],
            'question_id' => $question->id,
            'level' => strtolower($row['muc_do']),
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
            '*.noi_dung_cau_hoi' => 'required',
            '*.muc_do' => 'required|in:easy,medium,hard,EASY,MEDIUM,HARD',
            '*.dap_an_dung' => 'required',
            '*.dap_an_sai_1' => 'required',
            '*.dap_an_sai_2' => 'required',
            '*.dap_an_sai_3' => 'required'
        ];
    }

    public function customValidationMessages()
    {
        return [
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
