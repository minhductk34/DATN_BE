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
}
