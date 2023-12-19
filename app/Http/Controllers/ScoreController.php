<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Score;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;


class ScoreController extends Controller
{
    //

    public function store(Request $request) {

         try {
            $validatedData = $request->validate([
            'score' => 'required',
            'number_of_questions' => 'required',
            'user_id' => 'required', // Assuming user_id is an integer
            ]);

            $score = new Score();
            $score->score = $request['score'];
            $score->number_of_questions = $request['number_of_questions'];
            $score->user_id = $request['user_id'];

            $score->save();

        return response()->json(['message' => "Score recorded successfully!"]);
         } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
         }
    }

    public function home(Score $score, User $user) {

      try {
          $lastFiveQuestions = Score::latest()->take(5)->get();

        $arrayLastFive = [];

        foreach ($lastFiveQuestions as $score) {
            $userToFind = $user::findOrFail($score['user_id']);
            $score->username = $userToFind->username;
            array_push($arrayLastFive, $score);
        }

        return response()->json(['message' => $arrayLastFive]);

      } catch(Exception $e) {

        return response()->json(['error' => $e->getMessage()], 422);
      }
    }

    public function myScores(Score $score, $id) {

     try {
       $myScores = $score::where('user_id', $id)->latest()->paginate(5);

        return response()->json(['message' => $myScores]);
     } catch (Exception $e) {

      return response()->json(['error' => $e->getMessage()], 500);

    }
}
}