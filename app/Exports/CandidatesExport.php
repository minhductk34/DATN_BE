<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CandidatesExport implements WithMultipleSheets
{
    protected $candidatesByRoom;

    public function __construct($candidatesByRoom)
    {
        $this->candidatesByRoom = $candidatesByRoom;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->candidatesByRoom as $room => $candidates) {
            $sheets[] = new CandidatesByRoomSheet($room, $candidates);
        }

        return $sheets;
    }
}

class CandidatesByRoomSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $room;
    protected $candidates;

    public function __construct($room, $candidates)
    {
        $this->room = $room;
        $this->candidates = $candidates;
    }

    public function collection()
    {
        return collect($this->candidates)->map(function ($candidate) {
            return [
                $candidate->Idcode,
                $candidate->exam_id,
                $candidate->Fullname,
                $candidate->Image,
                $candidate->DOB,
                $candidate->Address,
                $candidate->exam_room_id,
                $candidate->Password,
                $candidate->Email,
                $candidate->Status
            ];
        });
    }

    public function title(): string
    {
        return $this->room;
    }

    public function headings(): array
    {
        return [
            'Idcode',
            'Exam ID',
            'Fullname',
            'Image',
            'DOB',
            'Address',
            'Examination Room',
            'Password',
            'Email',
            'Status'
        ];
    }
}
