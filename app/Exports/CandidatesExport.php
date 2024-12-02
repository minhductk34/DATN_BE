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
        $rooms = $this->candidatesByRoom->groupBy('name_room');

        $sheets = [];
        foreach ($rooms as $room => $candidates) {
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
                $candidate->name_room,
                $candidate->idcode,
                $candidate->name_candidate,
                $candidate->password,
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
            'Phòng thi',
            'Mã số thí sinh',
            'Tên thí sinh',
            'Mật khẩu'
        ];
    }
}
