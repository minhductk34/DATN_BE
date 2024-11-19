<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reading\StoreReadingRequest;
use App\Models\Reading;
use Illuminate\Http\Request;
use App\Http\Requests\ImportExelRequest;
use App\Http\Requests\Reading\UpdateReadingRequest;
use App\Imports\ReadingImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReadingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $exam_content_id = $request->input('exam_content_id');

            if (! $exam_content_id) {
                return $this->jsonResponse(false, null, 'Hãy chọn nội dung thi', 400);
            }

            $readings = Reading::query()
                ->where('exam_content_id', $exam_content_id)
                ->select('id', 'title', 'status', 'level')
                ->get();

            if ($readings->isEmpty()) {
                return $this->jsonResponse(true, [], 'Không tìm thấy bài đọc', 404);
            }

            return $this->jsonResponse(true, $readings, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function store(StoreReadingRequest $request)
    {
        $validatedData = $request->except('image');

        try {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $image = $request->file('image')->store('readings', 'public');
                $validatedData['image'] = $image;
            }

            $reading = Reading::create($validatedData);

            return $this->jsonResponse(true, $reading, '', 201);
        } catch (\Exception $e) {
            if (isset($image) && Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function importExcel(ImportExelRequest $request)
    {
        try {
            DB::beginTransaction();

            $import = new ReadingImport();
            $import->import($request->file('file'));

            if (count($import->failures()) > 0) {
                $failures = $import->failures();

                foreach ($failures as $failure) {
                    $errorMessages[] = [
                        'row' => $failure->row(),
                        'errors' => $failure->errors(),
                    ];
                }

                foreach ($import->imageTmp as $img) {
                    if (Storage::disk('public')->exists($img)) {
                        Storage::disk('public')->delete($img);
                    }
                }

                DB::rollBack();
                return $this->jsonResponse(false, null, $errorMessages, 422);
            }

            DB::commit();

            return $this->jsonResponse(true, [], '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, null, 'ID bài đọc không hợp lệ', 400);
            }

            $reading = Reading::with(['questions.currentVersion'])->find($id);

            if (!$reading) {
                return $this->jsonResponse(false, null, 'Không tìm thấy bài đọc', 404);
            }

            return $this->jsonResponse(true, $reading, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function update(UpdateReadingRequest $request, $id)
    {
        try {
            $reading = Reading::findOrFail($id);

            $validatedData = $request->except('image');

            $oldImage = $reading->image;
            $newImage = null;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $newImage = $request->file('image')->store('readings', 'public');
                $validatedData['image'] = $newImage;
            }

            $reading->update($validatedData);

            if ($newImage && $oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            return $this->jsonResponse(true, $reading, '', 200);
        } catch (ModelNotFoundException $e) {
            return $this->jsonResponse(false, null, 'Không tìm thấy bài đọc', 404);
        } catch (\Exception $e) {
            if ($newImage && Storage::disk('public')->exists($newImage)) {
                Storage::disk('public')->delete($newImage);
            }
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $reading = Reading::query()->select('id')->find($id);

            if (!$reading) {
                return $this->jsonResponse(false, null, 'Không tìm thấy bài đọc', 404);
            }

            $reading->delete();

            return $this->jsonResponse(true, null, 'Xóa thành công', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    protected function jsonResponse($success = true, $data = null, $message = '', $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'status' => "$statusCode",
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }
}
