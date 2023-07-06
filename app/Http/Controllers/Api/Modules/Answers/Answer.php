<?php

namespace App\Http\Controllers\Api\Modules\Answers;

use App\Http\Controllers\Api\Modules\UserCertificate\Certificate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"answer","question_id","is_correct"},
 * @OA\Xml(name="Answer"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="answer", type="string", readOnly="true",  description="answer unique name ", example="its good"),
 * @OA\Property(property="question_id", type="integer", readOnly="true",  description="id of the question ", example="1"),
 * @OA\Property(property="is_correct", type="integer", readOnly="true",  description=" 1 for the correct answer ", example="1"),
 * )
 */

class Answer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['is_correct', 'answer', 'question_id'];
    protected $hidden = ['deleted_at'];

    function Certificates()
    {
        return $this->belongsToMany(Certificate::class, 'certificate_answer');
    }

}
