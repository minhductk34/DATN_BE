<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\QuestionVersion;

class QuestionUpdate implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $spreadsheet;
    private $worksheet;
    private $currentRow = 2;
    public $imageTmp = [];

    public function __construct()
    {
        $this->spreadsheet = IOFactory::load(request()->file('file')->getPathname());
        $this->worksheet = $this->spreadsheet->getActiveSheet();
    }

    public function model(array $row)
    {
        $rowNumber = $this->currentRow;

        $image = $this->saveImageFromExcel($rowNumber, 'D');
        $image1 = $this->saveImageFromExcel($rowNumber, 'F');
        $image2 = $this->saveImageFromExcel($rowNumber, 'H');
        $image3 = $this->saveImageFromExcel($rowNumber, 'J');
        $image4 = $this->saveImageFromExcel($rowNumber, 'K');

        $this->currentRow++;

        $existingQuestion = Question::with(['currentVersion','versions'])->find($row['id']);

        return DB::transaction(function () use ($row, $image, $image1, $image2, $image3, $image4, $existingQuestion) {
            $existingQuestion->currentVersion->update(['is_active' => false]);

            $newVersion = new QuestionVersion([
                'question_id' => $existingQuestion->id,
                'title' => $row['question'],
                'image_Title' => $image,
                'answer_P' => $row['correct_answer'],
                'image_P' => $image1,
                'answer_F1' => $row['option2'],
                'image_F1' => $image2,
                'answer_F2' => $row['option3'],
                'image_F2' => $image3,
                'answer_F3' => $row['option4'],
                'image_F3' => $image4,
                'level' => $row['level'],
                'version' => $existingQuestion->versions()->max('version') + 1,
            ]);

            $newVersion->save();

            $existingQuestion->update([
                'id' => $row['id'],
                'current_version_id' => $newVersion->id,
                'exam_content_id' => $row['content_id']
            ]);

            return $newVersion;
        });
    }

    public function rules(): array
    {
        return [
            '*.id' => ['required', 'exists:questions,id', 'max:255'],
            '*.content_id' => ['required', 'exists:exam_contents,id', 'max:255'],
            '*.question' => ['required', 'string', 'max:255'],
            '*.correct_answer' => ['required', 'string', 'max:255'],
            '*.option2' => ['required', 'string', 'max:255'],
            '*.option3' => ['required', 'string', 'max:255'],
            '*.option4' => ['required', 'string', 'max:255'],
            '*.level' => ['required', 'in:easy,medium,difficult'],
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
