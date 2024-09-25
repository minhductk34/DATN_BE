<?php

namespace App\Http\Controllers;

use App\Imports\ExamContentImport;
use App\Models\ExamContent;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function getContentByExam($id)
    {
        try {

            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => '400',
                    'data' => [],
                    'warning' => 'Invalid exam_subject_id',
                ], 400);
            }

            $content = ExamContent::query()
                ->select('id', 'exam_subject_id', 'title', 'Status')
                ->where('exam_subject_id', $id)
                ->get();

            if ($content->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => '404',
                    'data' => [],
                    'warning' => 'No content found for the given exam_subject_id',
                ], 404);
            }


            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => $content,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id'=> 'required|string',
                'exam_subject_id' => 'required|string',
                'title' => 'required|string|max:255',
            ]);

            $examSubject = ExamContent::create($validatedData);

            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $examSubject,
                'warning' => 'Create exam content successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'data' => null,
                'warning' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'data' => null,
                'warning' => $e->getMessage()
            ], 500);
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ], [
            'file.required' => 'Hãy chọn một file để tải lên.',
            'file.mimes' => 'File không đúng định dạng ( .xlsx, .xls ).',
        ]);

        DB::beginTransaction();

        try {
            $import = new ExamContentImport();
            $import->import($request->file('file'));

            if (count($import->failures()) > 0) {
                $failures = $import->failures();
                $errorMessages = [];

                foreach ($failures as $failure) {
                    $errorMessages[] = [
                        'row' => $failure->row(),
                        'attribute' => $failure->attribute(),
                        'errors' => $failure->errors(),
                        'values' => $failure->values(),
                    ];
                }

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'status' => '422',
                    'data' => $errorMessages,
                    'warning' => 'Có lỗi xảy ra trong quá trình nhập dữ liệu. Vui lòng kiểm tra lại file và thử lại.',
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => '200',
                'data' => [],
                'warning' => 'Nhập dữ liệu thành công.',
            ], 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();

            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }

            return response()->json([
                'success' => false,
                'status' => '422',
                'data' => $errorMessages,
                'warning' => 'Có lỗi xảy ra trong quá trình nhập dữ liệu. Vui lòng kiểm tra lại file và thử lại.',
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'Đã xảy ra lỗi, vui lòng thử lại sau.',
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {

            if (!is_string($id) || empty(trim($id))) {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'data' => [],
                    'warning' => 'Invalid exam_content_id',
                ], 400);
            }

            $content = ExamContent::query()
                ->where('id', $id)
                ->get();

            if ($content->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'data' => [],
                    'warning' => 'No content found for the given exam_content_id',
                ], 404);
            }


            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $content,
                'warning' => '',
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'status' => '500',
                'data' => [],
                'warning' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamContent $exam_content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        try {
            $validatedData = $request->validate([
                'exam_subject_id' => 'required|string',
                'title' => 'required|string|max:255',
            ]);

            $examSubject = ExamContent::findOrFail($id);

            $examSubject->update($validatedData);

            $response = [
                'success' => true,
                'status' => 200,
                'data' => $examSubject,
                'warning' => 'update exam contetn successfully'
            ];

            return response()->json($response, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'data' => null,
                'warning' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'data' => null,
                'warning' => $e
            ], 500);
        }
    }

    public function updateStatus($id)
    {
        try {
            $examSubject = ExamContent::query()->find($id);

            if (!$examSubject) {
                return $this->jsonResponse(false, null, 'Không tìm thấy môn thi', 404);
            }

            $examSubject->Status = $examSubject->Status=='true' ? 'false' : 'true';
            
            $examSubject->save();

            return $this->jsonResponse(true, $examSubject->Status, 'update status exam content successfully', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamContent $exam_content)
    {
        //
    }
}
