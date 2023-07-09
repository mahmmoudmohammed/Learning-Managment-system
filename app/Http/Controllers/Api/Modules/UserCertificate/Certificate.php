<?php

namespace App\Http\Controllers\Api\Modules\UserCertificate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Api\Modules\Users\User;
//use App\Http\Controllers\Api\Modules\UserCertificate;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 *
 * @OA\Schema(
 * required={"title","topic_id"},
 * @OA\Xml(name="Certificate"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="title", type="text", readOnly="true", example="PHP advanced"),
 * @OA\Property(property="level", type="integer",description="the diffeculty of this cetificate" ,readOnly="true", example="1"),
 * @OA\Property(property="number", type="integer",description="The number of questions this certificate" ,readOnly="true", example="30"),
 * @OA\Property(property="duration", type="integer",description="The duration of this certificate" ,readOnly="true", example="30"),
 * @OA\Property(property="topic_id", type="integer", readOnly="true", format="integer", description="The topic of this certificate", example="1"),
 * )
 */
class Certificate extends Model
{
    use HasFactory, SoftDeletes;
    protected $table="certificates";
    protected $fillable = ['title','topic_id','level','number','duration'];

   public function users()
    {
        return $this->belongsToMany(User::class,'user_certificate')->withPivot('credintials','completed','result');
    }

    public function answers()
    {
        return $this->belongsToMany(Answer::class,'certificate_answer')->withPivot('is_correct');
    }

}
