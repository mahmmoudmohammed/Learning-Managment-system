<?php

namespace App\Http\Controllers\Api\Modules\Users;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Controllers\Api\Modules\Roles\Role;
use App\Http\Controllers\Api\Modules\Certificates\Certificate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 *
 * @OA\Schema(
 * required={"password"},
 * @OA\Xml(name="User"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 * @OA\Property(property="email", type="string", readOnly="true", format="email", description="User unique email address", example="user@gmail.com"),
 * )
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'name', 'email', 'password','role_id',
    ];

    const normalUser = 'user';
    const adminUser = 'admin';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function certificates()
    {
        return $this->belongsToMany(Certificate::class,'user_certificate',
            'user_id','certificate_id','id','id');
    }
}
