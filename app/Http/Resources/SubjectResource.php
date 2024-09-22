<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $questionCount = 0;

        foreach ($this->contents as $content) {
            // Câu hỏi thông thường liên kết trực tiếp với nội dung
            if ($content->questions) {
                $questionCount += $content->questions->count();
            }

            // Câu hỏi liên kết với từng bài đọc
            if ($content->readings) {
                foreach ($content->readings as $reading) {
                    if ($reading->questions) {
                        $questionCount += $reading->questions->count();
                    }
                }
            }

            // Câu hỏi liên kết với từng bài nghe
            if ($content->listenings) {
                foreach ($content->listenings as $listening) {
                    if ($listening->questions) {
                        $questionCount += $listening->questions->count();
                    }
                }
            }
        }

        return [
            'id'             => $this->id,
            'name'           => $this->Name,
            'question_count' => $questionCount,
        ];
    }
}
