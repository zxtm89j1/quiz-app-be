<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Choice;
use App\Models\Question;
use App\Models\User;
use Exception;
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
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
        
    }

    public function play(Question $question) {

        try {
            $this->authorize("viewall", $question);

         $questions = Question::with('choices')->get();
        return response()->json(['success' => $questions]);

        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
        
    }

    public function myQuestions(Question $questions, $id) {

        try {
            $myQuestions = $questions::with('choices')->where('user_id', $id)->get();
    
        return response()->json(['success' => $myQuestions]);
        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);

        }
    }

    public function editQuestion(Question $question, Choice $choice, Request $request, $id) {

        try {

             $validatedData = $request->validate([
            'question_text' => 'required|string',
            'correct_answer' => 'required',
            'choices' => 'required|array',
            'choices.*.value_in_frontend' => 'required|string',
            'choices.*.choice_text' => 'required|string',
            ]);

              $this->authorize('update', $question);

              $questionToUpdate = Question::findOrFail($id);

              if ($request->question_text === $questionToUpdate->question_text &&
                $request->correct_answer === $questionToUpdate->correct_answer &&
                $request->choices === $questionToUpdate->choices->toArray()) {
                return response()->json(['error' => "The provided data matches the existing information in the database. No changes were made to update the question."], 422);
            }

             $questionToUpdate->question_text = $validatedData['question_text'];

             $questionToUpdate->save();

             $choices = $request->choices;

             $array = [];

             foreach($choices as $choiceFromReq) {
                $choiceToUpdate = $choice::findOrFail($choiceFromReq['id']);

                $choiceToUpdate->choice_text = $choiceFromReq['choice_text'];

                $choiceToUpdate->save();
             }

             
    return response()->json(['message' => "Question updated successfully!"]);

            //  return response()->json(['success' => "Question updated successfully!"]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
       
    }


    public function deleteQuestion (Question $question, $id) {
      try {
          $this->authorize('delete', $question);

          $questionToBeDeleted = Question::findOrFail($id)->delete(); 


         return response()->json(['message' => "Question deleted successfully!"]);

      } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 422);        
      }

    }

    // public function post (Question $question) {

    //      $this->authorize('forceDelete', $question);
        


    //      return response()->json(['message' => "Sample new route if same ba!!"]);

    // }
}
