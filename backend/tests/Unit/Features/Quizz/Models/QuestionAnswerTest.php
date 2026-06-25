<?php

use App\Features\Quizz\Models\Answer;
use App\Features\Quizz\Models\Question;
use App\Features\Quizz\Models\Quizz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates questions with uuid primary keys and quizz relationships', function () {
    $question = Question::factory()->create([
        'question' => 'What is Laravel?',
        'position' => 1,
        'image_url' => 'https://example.com/question.png',
    ]);

    expect($question->id)->toBeString()
        ->and($question->getKeyType())->toBe('string')
        ->and($question->getIncrementing())->toBeFalse()
        ->and($question->quizz)->toBeInstanceOf(Quizz::class)
        ->and($question->question)->toBe('What is Laravel?')
        ->and($question->position)->toBe(1)
        ->and($question->image_url)->toBe('https://example.com/question.png');
});

it('creates answers with uuid primary keys and question relationships', function () {
    $answer = Answer::factory()->create([
        'answer' => 'A PHP framework',
        'is_correct' => true,
    ]);

    expect($answer->id)->toBeString()
        ->and($answer->getKeyType())->toBe('string')
        ->and($answer->getIncrementing())->toBeFalse()
        ->and($answer->question)->toBeInstanceOf(Question::class)
        ->and($answer->answer)->toBe('A PHP framework')
        ->and($answer->is_correct)->toBeTrue();
});

it('loads answer options from a question', function () {
    $question = Question::factory()
        ->has(Answer::factory()->count(4), 'answers')
        ->create();

    expect($question->answers)->toHaveCount(4)
        ->and($question->answers->first())->toBeInstanceOf(Answer::class);
});
