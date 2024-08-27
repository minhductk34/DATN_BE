<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

class QuestionUpdate implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithUpserts
{
    use Importable, SkipsFailures;

    private $currentRow = 2;
    public $imageTmp = [];

    public function model(array $row)
    {
        $rowNumber = $this->currentRow;

        $image = $this->saveImageFromExcel($rowNumber, 'D');
        $image1 = $this->saveImageFromExcel($rowNumber, 'F');
        $image2 = $this->saveImageFromExcel($rowNumber, 'H');
        $image3 = $this->saveImageFromExcel($rowNumber, 'J');
        $image4 = $this->saveImageFromExcel($rowNumber, 'K');

        $this->currentRow++;

        $existingQuestion = Question::find($row['id']);

        // Xóa ảnh cũ nếu có ảnh mới
        if ($existingQuestion) {
            $this->deleteOldImage($existingQuestion->Image_Title, $image);
            $this->deleteOldImage($existingQuestion->Image_P, $image1);
            $this->deleteOldImage($existingQuestion->Image_F1, $image2);
            $this->deleteOldImage($existingQuestion->Image_F2, $image3);
            $this->deleteOldImage($existingQuestion->Image_F3, $image4);
        }

        return new Question([
            'id' => $row['id'],
            'exam_content_id' => $row['content_id'],
            'Title' => $row['question'],
            'Image_Title' => $image,
            'Answer_P' => $row['correct_answer'],
            'Image_P' => $image1,

            'Answer_F1' => $row['option2'],
            'Image_F1' => $image2,
            'Answer_F2' => $row['option3'],
            'Image_F2' => $image3,
            'Answer_F3' => $row['option4'],
            'Image_F3' => $image4,

            'Level' => $row['level']
        ]);
    }

    public function uniqueBy()
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required|max:255',
            '*.content_id' => 'required|exists:exam_contents,id|max:255',
            '*.question' => 'required|string|max:255',
            '*.image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            '*.correct_answer' => 'required|string|max:255',
            '*.image1' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',

            '*.option2' => 'required|string|max:255',
            '*.image2' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            '*.option3' => 'required|string|max:255',
            '*.image3' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
            '*.option4' => 'required|string|max:255',
            '*.image4' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',

            '*.level' => 'required|in:Easy,Medium,Difficult',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.id.required' => ':attribute bắt buộc phải nhập',
            '*.content_id.required' => ':attribute bắt buộc phải nhập',
            '*.question.required' => ':attribute bắt buộc phải nhập',
            '*.correct_Answer.required' => ':attribute bắt buộc phải nhập',
            '*.option2.required' => ':attribute bắt buộc phải nhập',
            '*.option3.required' => ':attribute bắt buộc phải nhập',
            '*.option4.required' => ':attribute bắt buộc phải nhập',
            '*.level.required' => ':attribute bắt buộc phải nhập',

            '*.content_id.exists' => ':attribute không tồn tại',

            '*.question.string' => ':attribute phải là chuỗi',
            '*.option2.string' => ':attribute phải là chuỗi',
            '*.option3.string' => ':attribute phải là chuỗi',
            '*.option4.string' => ':attribute phải là chuỗi',

            '*.id.max' => ':attribute tối đa :max kí tự',
            '*.content_ID.max' => ':attribute tối đa :max kí tự',
            '*.correct_Answer.max' => ':attribute tối đa :max kí tự',
            '*.option2.max' => ':attribute tối đa :max kí tự',
            '*.option3.max' => ':attribute tối đa :max kí tự',
            '*.option4.max' => ':attribute tối đa :max kí tự',

            '*.image.file' => ':attribute phải là một file',
            '*.image1.file' => ':attribute phải là một file',
            '*.image2.file' => ':attribute phải là một file',
            '*.image3.file' => ':attribute phải là một file',
            '*.image4.file' => ':attribute phải là một file',

            '*.image.mimes' => ':attribute không đúng định dạng file (jpeg,jpg,png,gif,webp)',
            '*.image1.mimes' => ':attribute không đúng định dạng file (jpeg,jpg,png,gif,webp)',
            '*.image2.mimes' => ':attribute không đúng định dạng file (jpeg,jpg,png,gif,webp)',
            '*.image3.mimes' => ':attribute không đúng định dạng file (jpeg,jpg,png,gif,webp)',
            '*.image4.mimes' => ':attribute không đúng định dạng file (jpeg,jpg,png,gif,webp)',

            '*.level.in' => ':attribute không hợp lệ ( easy,medium,difficult )',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '*.id' => 'Mã câu hỏi',
            '*.content_id' => 'Mã nội dung thi',
            '*.question' => 'Nội dung câu hỏi',
            '*.image' => 'Ảnh câu hỏi',
            '*.correct_answer' => 'Đáp án đúng',
            '*.image1' => 'Ảnh đáp án đúng',

            '*.option2' => 'Đáp án sai 1',
            '*.image2' => 'Ảnh đáp án sai 1',
            '*.option3' => 'Đáp án sai 2',
            '*.image3' => 'Ảnh đáp án sai 2',
            '*.option4' => 'Đáp án sai 3',
            '*.image4' => 'Ảnh đáp án sai 3',

            '*.level' => 'Mức độ'
        ];
    }

    private function saveImageFromExcel($rowNumber, $column)
    {
        // Đọc file Excel hiện tại
        $spreadsheet = IOFactory::load(request()->file('file')->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();

        // Duyệt qua tất cả các hình ảnh trong worksheet
        foreach ($worksheet->getDrawingCollection() as $drawing) {
            if ($drawing->getCoordinates() === $column . $rowNumber) {
                // Lấy thông tin hình ảnh
                $imageContents = $this->getImageContents($drawing);
                $imageName = time() . '-' . uniqid() . '.' . $drawing->getExtension();
                $imagePath = 'questions/' . $imageName;
                $this->imageTmp[] = $imagePath;

                // Lưu hình ảnh vào thư mục public
                Storage::disk('public')->put($imagePath, $imageContents);

                return $imagePath;
            }
        }

        return null;
    }

    /**
     * Lấy nội dung hình ảnh từ đối tượng Drawing.
     */
    private function getImageContents($drawing)
    {
        // Kiểm tra loại của hình ảnh (định dạng PNG hoặc JPEG)
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

    private function deleteOldImage($oldImage, $newImage)
    {
        if ($newImage && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }
    }
}
