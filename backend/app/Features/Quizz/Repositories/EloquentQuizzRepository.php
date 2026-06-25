<?php

namespace App\Features\Quizz\Repositories;

use App\Features\Course\Models\Course;
use App\Features\Quizz\Contracts\QuizzRepositoryContract;
use App\Features\Quizz\DTOs\QuestionCreateData;
use App\Features\Quizz\DTOs\QuizzCreateData;
use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\Quizz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class EloquentQuizzRepository implements QuizzRepositoryContract
{
    public function findCourseById(string $courseId): ?Course
    {
        return Course::query()->find($courseId);
    }

    public function findFinalQuizByCourse(Course $course): ?Quizz
    {
        return Quizz::query()
            ->where('course_id', $course->id)
            ->first();
    }

    public function createFinalQuiz(Course $course, QuizzCreateData $data): Quizz
    {
        return Quizz::query()->create([
            'course_id' => $course->id,
            'quiz_name' => $data->quizName,
            'is_active' => $data->isActive,
        ]);
    }

    public function findQuizById(string $quizId): ?Quizz
    {
        return Quizz::query()->find($quizId);
    }

    public function updateQuiz(Quizz $quiz, QuizzCreateData $data): Quizz
    {
        $quiz->update([
            'quiz_name' => $data->quizName,
            'is_active' => $data->isActive,
        ]);

        return $quiz->refresh();
    }

    public function deleteQuiz(Quizz $quiz): bool
    {
        return (bool) $quiz->delete();
    }

    public function listQuestionsWithAnswers(Quizz $quiz): Collection
    {
        return Question::query()
            ->with('answers')
            ->where('quizz_id', $quiz->id)
            ->orderBy('position')
            ->get();
    }

    public function createQuestionsWithAnswers(Quizz $quiz, array $questions): Collection
    {
        return DB::transaction(function () use ($quiz, $questions): Collection {
            return collect($questions)
                ->map(function (QuestionCreateData $data) use ($quiz): Question {
                    $question = Question::query()->create([
                        'quizz_id' => $quiz->id,
                        'question' => $data->question,
                        'position' => $data->position,
                        'image_url' => $data->imageUrl,
                    ]);

                    $question->answers()->createMany(
                        array_map(fn ($answer): array => [
                            'answer' => $answer->answer,
                            'is_correct' => $answer->isCorrect,
                        ], $data->answers),
                    );

                    return $question->load('answers');
                });
        });
    }
}
