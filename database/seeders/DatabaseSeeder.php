<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ExamSeeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LecturersSeeder::class,
            AdminSeeder::class,
            ExamSeeder::class,
            ExamRoomSeeder::class,
            CandidateSeeder::class,
            ExamSubjectSeeder::class,
            ExamSessionSeeder::class,
            PasswordSeeder::class,
            ActiveSeeder::class,
            PointSeeder::class,
            ExamContentSeeder::class,
            HistorySeeder::class,
            ListeningSeeder::class,
            ReadingsSeeder::class,
            TopicStructureSeeder::class,
            QuestionSeeder::class,
            ListeningQuestionsSeeder::class,
            ReadingQuestionsSeeder::class,
            CandidateQuestionSeeder::class,
            CandidatesTakeTheEnglishTestSeeder::class,
            ExamRoomDetailSeeder::class,
//            QuestionVersionSeeder::class,
//            RedingQuestionVersionSeeder::class,
//            ListeningQuestionVersionSeeder::class,
            ExamSubjectDetailsSeeder::class,
        ]);
    }

}
