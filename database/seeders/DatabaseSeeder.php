<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\English_exam_question;
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
            AdminSeeder::class,
            LecturerSeeder::class,
            ExamSeeder::class,
            ExamRoomSeeder::class,
            ExamSubjectSeeder::class,
            ExamSessionSeeder::class,
            ExamSubjectDetailSeeder::class,
            ExamRoomDetailSeeder::class,
            CandidateSeeder::class,
            PasswordSeeder::class,
            ActiveSeeder::class,
            HistorySeeder::class,
            ExamContentSeeder::class,
            ExamStructureSeeder::class,
            PointSeeder::class,
            ListeningSeeder::class,
            ReadingSeeder::class,
            QuestionSeeder::class,
            ListeningQuestionSeeder::class,
            ReadingQuestionSeeder::class,
//            QuestionVersionSeeder::class,
//            ListeningQuestionVersionSeeder::class,
//            ReadingQuestionVersionSeeder::class,
            CandidateQuestionSeeder::class,
            EnglishExamQuestionSeeder::class
        ]);
    }

}
