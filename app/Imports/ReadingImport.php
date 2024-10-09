<?php

namespace App\Imports;

use App\Models\Reading;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ReadingImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
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

        $image = $this->saveImageFromExcel($rowNumber, 'E');

        $this->currentRow++;

        return new Reading([
            'id' => $row['id'],
            'exam_content_id' => $row['exam_content_id'],
            'Title' => $row['reading'],
            'Level' => $row['level'],
            'Image' => $image,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required|unique:readings,id',
            '*.exam_content_id' => 'required|exists:exam_contents,id',
            '*.reading' => 'required',
            '*.level' => 'nullable|in:Easy,Medium,Difficult',
            '*.image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.id.required' => ':attribute bắt buộc phải nhập',
            '*.exam_content_id.required' => ':attribute bắt buộc phải nhập',
            '*.reading.required' => ':attribute bắt buộc phải nhập',
            '*.id.unique' => ':attribute đã tồn tại',
            '*.exam_content_id.exists' => ':attribute không tồn tại',
            '*.level.in' => ':attribute không hợp lệ',
            '*.image.file' => ':attribute phải là một file',
            '*.image.mimes' => ':attribute không đúng định dạng file (jpeg,jpg,png,gif,webp)',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            '*.id' => 'Mã bài đọc',
            '*.exam_content_id' => 'ID nội dung thi',
            '*.reading' => 'Bài đọc',
            '*.level' => 'Độ khó',
            '*.image' => 'Ảnh',
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
