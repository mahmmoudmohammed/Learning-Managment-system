<?php

namespace App\Http\Controllers\Api\Modules\Roles;

use App\Http\Controllers\Api\Modules\Permissions\Permission;
use App\Http\Controllers\Api\Modules\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @OA\Schema(
 * required={"title"},
 * @OA\Xml(name="Role"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="title", type="string", readOnly="true",  description="Role unique name ", example="admin"),
 * )
 */
class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title'];
    protected $hidden = ['deleted_at', 'pivot'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
