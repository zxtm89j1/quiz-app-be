<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'score',
        'number_of_questions',
        'user_id',
    ];


    public function scores()
    {
        return $this->belongsTo(User::class);
    }
}
