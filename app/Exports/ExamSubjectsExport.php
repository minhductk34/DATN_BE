<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamSubjectsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $subjects;

    public function __construct($subjects)
    {
        $this->subjects = $subjects;
    }

    public function collection()
    {
        return $this->subjects;
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->exam_id,
            $row->name,
            $row->status ? 'Hoạt động' : 'Không hoạt động'
        ];
    }

    public function headings(): array
    {
        return [
            'Mã môn thi',
            'Mã kỳ thi',
            'Tên môn thi',
            'Trạng thái'
        ];
    }
}
