<?php

namespace App\Http\Controllers\Api\Modules\Topics;

use App\Http\Controllers\Api\Modules\Categories\Category;
use App\Http\Controllers\Api\Modules\Questions\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"title"},
 * @OA\Xml(name="Topic"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="title", type="string", readOnly="true",  description="Topic unique name ", example="PHP"),
 * )
 */
class Topic extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title', 'category_id', 'status'];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
