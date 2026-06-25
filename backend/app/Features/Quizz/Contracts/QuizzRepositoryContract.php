<?php

namespace App\Features\Quizz\Contracts;

use App\Features\Course\Models\Course;
use App\Features\Quizz\DTOs\QuestionCreateData;
use App\Features\Quizz\DTOs\QuestionUpdateData;
use App\Features\Quizz\DTOs\QuizzCreateData;
use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\Quizz;
use Illuminate\Support\Collection;

interface QuizzRepositoryContract
{
    public function findCourseById(string $courseId): ?Course;

    public function findFinalQuizByCourse(Course $course): ?Quizz;

    public function createFinalQuiz(Course $course, QuizzCreateData $data): Quizz;

    public function findQuizById(string $quizId): ?Quizz;

    public function findQuestionById(string $questionId): ?Question;

    public function updateQuiz(Quizz $quiz, QuizzCreateData $data): Quizz;

    public function deleteQuiz(Quizz $quiz): bool;

    public function deleteQuestion(Question $question): bool;

    /**
     * @return Collection<int, Question>
     */
    public function listQuestionsWithAnswers(Quizz $quiz): Collection;

    /**
     * @param  array<int, QuestionCreateData>  $questions
     * @return Collection<int, Question>
     */
    public function createQuestionsWithAnswers(Quizz $quiz, array $questions): Collection;

    public function updateQuestionWithAnswers(Question $question, QuestionUpdateData $data): Question;
}
