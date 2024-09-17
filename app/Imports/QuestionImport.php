<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\QuestionVersion;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class QuestionImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithChunkReading
{
    use Importable, SkipsFailures;

    private $spreadsheet;
    private $worksheet;
    public $imageTmp = [];

    public function __construct()
    {
        $this->spreadsheet = IOFactory::load(request()->file('file')->getPathname());
        $this->worksheet = $this->spreadsheet->getActiveSheet();
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $questions = [];
            $versions = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                $questionId = $row['id'];
                $questions[] = [
                    'id' => $questionId,
                    'exam_content_id' => $row['content_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $versions[] = [
                    'question_id' => $questionId,
                    'Title' => $row['question'],
                    'Image_Title' => $this->saveImageFromExcel($rowNumber, 'D'),
                    'Answer_P' => $row['correct_answer'],
                    'Image_P' => $this->saveImageFromExcel($rowNumber, 'F'),
                    'Answer_F1' => $row['option2'],
                    'Image_F1' => $this->saveImageFromExcel($rowNumber, 'H'),
                    'Answer_F2' => $row['option3'],
                    'Image_F2' => $this->saveImageFromExcel($rowNumber, 'J'),
                    'Answer_F3' => $row['option4'],
                    'Image_F3' => $this->saveImageFromExcel($rowNumber, 'K'),
                    'Level' => $row['level'],
                    'version' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Question::insert($questions);
            QuestionVersion::insert($versions);

            $versionIds = QuestionVersion::whereIn('question_id', array_column($questions, 'id'))
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
                Question::where('id', $update['id'])->update([
                    'current_version_id' => $update['current_version_id'],
                    'updated_at' => $update['updated_at']
                ]);
            }
        });
    }

    public function rules(): array
    {
        return [
            '*.id' => ['required', 'unique:questions,id', 'max:255'],
            '*.content_id' => ['required', 'exists:exam_contents,id', 'max:255'],
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
            '*.content_id.exists' => ':attribute không tồn tại',
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
            'content_id' => 'Mã nội dung thi',
            'question' => 'Nội dung câu hỏi',
            'correct_answer' => 'Đáp án đúng',
            'option2' => 'Đáp án sai 1',
            'option3' => 'Đáp án sai 2',
            'option4' => 'Đáp án sai 3',
            'level' => 'Mức độ'
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    private function saveImageFromExcel($rowNumber, $column)
    {
        foreach ($this->worksheet->getDrawingCollection() as $drawing) {
            if ($drawing->getCoordinates() === $column . $rowNumber) {
                $imageContents = $this->getImageContents($drawing);
                $imageName = time() . '-' . uniqid() . '.' . $drawing->getExtension();
                $imagePath = 'questions/' . $imageName;
                $this->imageTmp[] = $imagePath;

                Storage::disk('public')->put($imagePath, $imageContents);

                return $imagePath;
            }
        }

        return null;
    }

    private function getImageContents($drawing)
    {
        if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
            ob_start();
            call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
            $imageContents = ob_get_contents();
            ob_end_clean();
        } else {
            $imageContents = file_get_contents($drawing->getPath());
        }

        return $imageContents;
    }
}
