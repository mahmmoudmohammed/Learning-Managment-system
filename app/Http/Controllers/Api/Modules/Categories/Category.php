<?php

namespace App\Http\Controllers\Api\Modules\Categories;

use App\Http\Controllers\Api\Modules\Topics\Topic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"name"},
 * @OA\Xml(name="Category"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="name", type="string", readOnly="true",  description="Category unique name ", example="Backend"),
 * )
 */

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'parent_id'];


    public function categories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class, 'category_id');
    }
}
