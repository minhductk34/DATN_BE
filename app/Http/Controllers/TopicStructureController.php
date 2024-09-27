<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TopicStructure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TopicStructureController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'exam_content_id' => 'required',
                'exam_subject_id' => 'required|exists:exam_subjects,id',
                'level' => 'required|in:Easy,Medium,Difficult',
                'quality' => 'required|integer|between:1,32767',
            ]);

            $topicStructure = TopicStructure::create($validated);

            return response()->json($topicStructure, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create topic structure: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'exam_content_id' => 'required|exists:exam_contents,id',
                'level' => 'required|in:Easy,Medium,Difficult',
                'exam_subject_id' => 'required|exists:exam_subjects,id',
                'quality' => 'required|integer|between:1,32767',
            ]);

            $topicStructure = TopicStructure::findOrFail($id);

            if (empty($validated)) {
                return response()->json(['message' => 'No valid data provided for update.'], 400);
            }

            // Update the topic structure
            $topicStructure->update($validated);

            return response()->json($topicStructure);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic structure not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update topic structure: ' . $e->getMessage()], 500);
        }
    }

    // Phương thức để lấy thông tin TopicStructure theo ID
    public function show($id)
    {
        try {
            // Tìm kiếm TopicStructure theo ID
            $topicStructure = TopicStructure::findOrFail($id);

            return response()->json($topicStructure);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Topic structure not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve topic structure: ' . $e->getMessage()], 500);
        }
    }

    // Phương thức mới để lấy thông tin theo exam_subject_id
    public function showByExamSubjectId($exam_subject_id)
    {
        try {
            // Lấy danh sách TopicStructure theo exam_subject_id
            $topicStructures = TopicStructure::where('exam_subject_id', $exam_subject_id)->get();

            if ($topicStructures->isEmpty()) {
                return response()->json(['error' => 'No topic structures found for this exam_subject_id.'], 404);
            }

            return response()->json($topicStructures);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve topic structures: ' . $e->getMessage()], 500);
        }
    }
}
