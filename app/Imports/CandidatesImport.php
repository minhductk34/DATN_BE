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
            // Tìm kỳ thi mới nhất
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
                $lastRoom = Exam_room::where('exam_id', $exam->id)
                    ->orderBy('name', 'desc')
                    ->first();

                $roomNumber = $lastRoom ? intval(substr($lastRoom->name, 6)) + 1 : 1;
                $examRoom = Exam_room::create([
                    'exam_id' => $exam->id,
                    'name' => 'Phòng ' . $roomNumber
                ]);
            }

            // Tạo mật khẩu ngẫu nhiên
            $password = Str::random(8);

            // Tạo candidate mới
            $candidate = Candidate::create([
                'idcode' => $row['ma_thi_sinh'],
                'exam_id' => $exam->id,
                'exam_room_id' => $examRoom->id,
                'name' => $row['ten'],
                'dob' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngay_sinh']),
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

    public function rules(): array
    {
        return [
            '*.ma_thi_sinh' => 'required|unique:candidates,idcode',
            '*.ten' => 'required',
            '*.ngay_sinh' => 'required',
            '*.dia_chi' => 'required',
            '*.email' => 'required|email|unique:candidates,email'
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.ma_thi_sinh.required' => 'Mã thí sinh là bắt buộc',
            '*.ma_thi_sinh.unique' => 'Mã thí sinh đã tồn tại',
            '*.ten.required' => 'Tên thí sinh là bắt buộc',
            '*.ngay_sinh.required' => 'Ngày sinh là bắt buộc',
            '*.dia_chi.required' => 'Địa chỉ là bắt buộc',
            '*.email.required' => 'Email là bắt buộc',
            '*.email.email' => 'Email không đúng định dạng',
            '*.email.unique' => 'Email đã tồn tại'
        ];
    }
}
