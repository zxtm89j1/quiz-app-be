<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Choice;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;


class QuestionController extends Controller
{
    //
    public function store(Request $request, Question $question) {
        $this->authorize("create", $question);
        try {
            $validatedData = $request->validate([
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'user_id' => 'required|integer', // Assuming user_id is an integer
            'choices' => 'required|array',
            'choices.*.value_in_frontend' => 'required|string',
            'choices.*.choice_text' => 'required|string',
            ]);

        // Create a new question
        $question_create = Question::create([
            'question_text' => $validatedData['question_text'],
            'correct_answer' => $validatedData['correct_answer'],
            'user_id' => $validatedData['user_id'],
        ]);
        

           foreach ($validatedData['choices'] as $choiceData) {
                $choice = new Choice();
                $choice->value_in_frontend = $choiceData['value_in_frontend'];
                $choice->choice_text = $choiceData['choice_text'];

                // Saves the question and its choices in the db using the Eloquent relationship in Laravel
                $question_create->choices()->save($choice);
           }

            return response()->json(['success' => "Question saved successfully!!!"]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
        
    }

    public function play(Question $question) {
        $this->authorize("viewall", $question);

         $questions = Question::with('choices')->get();
        return response()->json(['success' => $questions]);
    }

    public function myQuestions(Question $questions, $id) {

        $myQuestions = $questions::with('choices')->where('user_id', $id)->get();
        // $myQuestions = $question::all();
        return response()->json(['success' => $myQuestions]);
    }
}
