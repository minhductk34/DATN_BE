<?php

namespace App\Imports;

use App\Http\Controllers\ExamRoomController;
use App\Models\Candidate;
use App\Models\Exam;
use App\Models\Exam_room;
use App\Models\Password;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class CandidatesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        try {
            // Tương tự như phần store của CandidateController
            $exam = Exam::query()->select('id')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$exam) {
                throw new \Exception('Không tìm thấy kỳ thi');
            }

            // Tìm phòng thi còn trống
            $examRoom = Exam_room::withCount('candidates')
                ->where('exam_id', $exam->id)
                ->having('candidates_count', '<', 35)
                ->first();

            if (!$examRoom) {
                // Tự tạo phòng mới nếu không có phòng trống
                $ExamRoomController = new ExamRoomController();
                $examRoom = $ExamRoomController->createRoom('Phòng tự sinh', $exam->id);
            }

            // Tạo mật khẩu ngẫu nhiên như phần thêm mới
            $password = Str::random(8);

            // Xử lý ngày sinh từ Excel - format dd-mm-yyyy
            $dob = date('Y-m-d', strtotime($row['ngay_sinh']));

            // Tạo candidate mới
            $candidate = Candidate::create([
                'idcode' => $row['ma_thi_sinh'],
                'exam_id' => $exam->id,
                'exam_room_id' => $examRoom->id,
                'name' => $row['ten'],
                'dob' => $dob,
                'address' => $row['dia_chi'],
                'email' => $row['email'],
                'image' => 'default/user.png',
                'status' => true
            ]);

            // Tạo password record
            Password::create([
                'idcode' => $candidate->idcode,
                'password' => Crypt::encrypt($password)
            ]);

            return $candidate;

        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            throw $e;
        }
    }

// Map tên cột từ Excel sang tên field
    public function map($row): array
    {
        return [
            'ma_thi_sinh' => $row['Ma thi sinh'],
            'ten' => $row['Ten'],
            'ngay_sinh' => $row['Ngay sinh'],
            'dia_chi' => $row['Dia chi'],
            'email' => $row['Email']
        ];
    }

// Validate dữ liệu import
    public function rules(): array
    {
        return [
            'ma_thi_sinh' => 'required|unique:candidates,idcode',
            'ten' => 'required',
            'ngay_sinh' => 'required|date_format:d-m-Y',
            'dia_chi' => 'required',
            'email' => 'required|email|unique:candidates,email'
        ];
    }

    public function headingRow(): int
    {
        return 1; // Returns first row as the header row
    }
}
