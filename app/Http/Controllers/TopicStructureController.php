<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TopicStructure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class TopicStructureController extends Controller
{


    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_content_id' => 'required|exists:exam_contents,id',
            'level' => 'required|in:Easy,Medium,Difficult',
            'quality' => 'required|integer|between:1,32767',
        ]);



        $topicStructure = TopicStructure::create($validated);

        return response()->json($topicStructure, 201);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'exam_content_id' => 'required|exists:exam_contents,id',
            'level' => 'required|in:Easy,Medium,Difficult',
            'quality' => 'required|integer|between:1,32767',
        ]);

        $topicStructure = TopicStructure::findOrFail($id);
        $topicStructure->update($validated);

        return response()->json($topicStructure);
    }
}
