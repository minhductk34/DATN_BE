<?php

namespace App\Imports;

use App\Models\ReadingQuestion;
use App\Models\ReadingQuestionVersion;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ReadingQuestionImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $questions = [];
            $versions = [];

            foreach ($rows as $row) {
                $questionId = $row['id'];
                $questions[] = [
                    'id' => $questionId,
                    'reading_id' => $row['reading_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $versions[] = [
                    'question_id' => $questionId,
                    'Title' => $row['question'],
                    'Answer_P' => $row['correct_answer'],
                    'Answer_F1' => $row['option2'],
                    'Answer_F2' => $row['option3'],
                    'Answer_F3' => $row['option4'],
                    'Level' => $row['level'],
                    'version' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ReadingQuestion::insert($questions);
            ReadingQuestionVersion::insert($versions);

            $versionIds = ReadingQuestionVersion::whereIn('question_id', array_column($questions, 'id'))
                ->pluck('id', 'question_id');

            $updates = [];
            foreach ($versionIds as $questionId => $versionId) {
                $updates[] = [
                    'id' => $questionId,
                    'current_version_id' => $versionId,
                    'updated_at' => now(),
                ];
            }

            foreach ($updates as $update) {
                ReadingQuestion::where('id', $update['id'])->update([
                    'current_version_id' => $update['current_version_id'],
                    'updated_at' => $update['updated_at']
                ]);
            }
        });
    }

    public function rules(): array
    {
        return [
            '*.id' => ['required', 'unique:reading_questions,id', 'max:255'],
            '*.reading_id' => ['required', 'exists:readings,id', 'max:255'],
            '*.question' => ['required', 'string', 'max:255'],
            '*.correct_answer' => ['required', 'string', 'max:255'],
            '*.option2' => ['required', 'string', 'max:255'],
            '*.option3' => ['required', 'string', 'max:255'],
            '*.option4' => ['required', 'string', 'max:255'],
            '*.level' => ['required', 'in:Easy,Medium,Difficult'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.*.required' => ':attribute bắt buộc phải nhập',
            '*.reading_id.exists' => ':attribute không tồn tại',
            '*.*.string' => ':attribute phải là chuỗi',
            '*.id.unique' => ':attribute đã tồn tại',
            '*.*.max' => ':attribute tối đa :max kí tự',
            '*.level.in' => ':attribute không hợp lệ (Easy, Medium, Difficult)',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'id' => 'Mã câu hỏi',
            'reading_id' => 'ID baì đọc',
            'question' => 'Nội dung câu hỏi',
            'correct_answer' => 'Đáp án đúng',
            'option2' => 'Đáp án sai 1',
            'option3' => 'Đáp án sai 2',
            'option4' => 'Đáp án sai 3',
            'level' => 'Mức độ'
        ];
    }
}
