<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChoiceController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('addscore', [ScoreController::class, 'store'])->middleware('isloggedin');
    Route::post('addquestion', [QuestionController::class, 'store'])->middleware('isloggedin');
    Route::put('editaccount/{id}', [UserController::class, 'editAccount'])->middleware('isloggedin');
    Route::get('play', [QuestionController::class, 'play'])->middleware('isloggedin');
    Route::get('home', [ScoreController::class, 'home']);
    Route::get('myaccount/{id}', [UserController::class, 'myAccount'])->middleware('isloggedin');
    Route::get('myquestions/{id}', [QuestionController::class, 'myQuestions'])->middleware('isloggedin');
    Route::get('myscores/{id}', [ScoreController::class, 'myScores'])->middleware('isloggedin');
    Route::patch('editquestion/{id}', [QuestionController::class, 'editQuestion'])->middleware('isloggedin');
    Route::delete('deletequestion/{id}', [QuestionController::class, 'deleteQuestion'])->middleware('isloggedin');
});
