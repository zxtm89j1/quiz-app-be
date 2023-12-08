<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

      protected $fillable = [
        'question_text',
        "correct_answer",
        "user_id"
    ];

    public function choices()
    {
        return $this->belongsTo(Question::class);
    }
}
