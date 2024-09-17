<?php

namespace App\Http\Controllers;

use App\Http\Requests\Listening\StoreListeningRequest;
use App\Http\Requests\Listening\UpdateListeningRequest;
use App\Models\Listening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListeningController extends Controller
{
    public function index(Request $request)
    {
        try {
            $exam_content_id = $request->input('exam_content_id');

            if (! $exam_content_id) {
                return $this->jsonResponse(false, null, 'Hãy chọn nội dung thi', 400);
            }

            $listenings = Listening::query()
                ->where('exam_content_id', $exam_content_id)
                ->select('id', 'Name', 'Status', 'Level')
                ->get();

            if ($listenings->isEmpty()) {
                return $this->jsonResponse(true, [], 'Không tìm bài nghe', 404);
            }

            return $this->jsonResponse(true, $listenings, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function store(StoreListeningRequest $request)
    {
        $validatedData = $request->except('Audio');

        try {
            if ($request->file('Audio')->isValid()) {
                $path = $request->file('Audio')->store('audio', 'public');
                $validatedData['Audio'] = $path;
            }

            $listening = Listening::create($validatedData);

            return $this->jsonResponse(true, $listening, '', 201);
        } catch (\Exception $e) {
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            if (!is_string($id) || empty(trim($id))) {
                return $this->jsonResponse(false, null, 'ID bài nghe không hợp lệ', 400);
            }

            $listening = Listening::with('questions.currentVersion')->find($id);

            if (!$listening) {
                return $this->jsonResponse(false, null, 'Không tìm thấy bài nghe', 404);
            }

            return $this->jsonResponse(true, $listening, '', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function update(UpdateListeningRequest $request, $id)
    {
        $validatedData = $request->except('Audio');

        try {
            $listening = Listening::find($id);

            if (!$listening) {
                return $this->jsonResponse(false, null, 'Không tìm thấy bài nghe', 404);
            }

            $oldAudio = $listening->Audio;
            $newAudio = null;

            if ($request->file('Audio')->isValid()) {
                $newAudio = $request->file('Audio')->store('audio', 'public');
                $validatedData['Audio'] = $newAudio;
            }

            $listening->update($validatedData);

            if ($newAudio && $oldAudio && Storage::disk('public')->exists($oldAudio)) {
                Storage::disk('public')->delete($oldAudio);
            }

            return $this->jsonResponse(true, $listening, '', 200);
        } catch (\Exception $e) {
            if ($newAudio && Storage::disk('public')->exists($newAudio)) {
                Storage::disk('public')->delete($newAudio);
            }
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $listening = Listening::query()->select('id')->find($id);

            if (!$listening) {
                return $this->jsonResponse(false, null, 'Không tìm thấy bài nghe', 404);
            }

            $listening->delete();

            return $this->jsonResponse(true, null, 'Xóa thành công', 200);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, null, $e->getMessage(), 500);
        }
    }

    protected function jsonResponse($success = true, $data = null, $warning = '', $statusCode = 200)
    {
        return response()->json([
            'success' => $success,
            'status' => "$statusCode",
            'data' => $data,
            'warning' => $warning
        ], $statusCode);
    }
}
