<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportQuestions implements FromCollection, WithHeadings, WithMapping
{
    protected $questions;

    public function __construct($questions)
    {
        $this->questions = $questions;
    }

    public function collection()
    {
        return $this->questions;
    }

    public function map($question): array
    {
        return [
            $question->id,
            $question->exam_content_id,
            $question->title,
            $question->level,
            $question->answer_P,
            $question->answer_F1,
            $question->answer_F2,
            $question->answer_F3,
            $question->status ? 'Hoạt động' : 'Không hoạt động'
        ];
    }

    public function headings(): array
    {
        return [
            'Mã câu hỏi',
            'Mã nội dung',
            'Nội dung câu hỏi',
            'Mức độ',
            'Đáp án đúng',
            'Đáp án sai 1',
            'Đáp án sai 2',
            'Đáp án sai 3',
            'Trạng thái'
        ];
    }
}
