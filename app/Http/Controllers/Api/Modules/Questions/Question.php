<?php

namespace App\Http\Controllers\Api\Modules\Questions;


use App\Http\Controllers\Api\Modules\Topics\Topic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Controllers\Api\Modules\Answers\Answer;

/**
 *
 * @OA\Schema(
 * required={"question","difficulty","topic_id"},
 * @OA\Xml(name="Question"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="question", type="string", readOnly="true",  description="Question unique name ", example="what's you'r name?"),
 * )
 */
class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['question', 'topic_id', 'difficulty'];
    protected $hidden = ['deleted_at'];

    const easy = 1;
    const medium = 2; //random questions
    const high = 3;


    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
