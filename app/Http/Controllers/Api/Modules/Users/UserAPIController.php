<?php

namespace App\Http\Controllers\Api\Modules\Users;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\Modules\Roles\Role;



class UserAPIController extends BaseController
{
    use ApiResponseTrait;
    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       @OA\Property(property="persistent", type="boolean", example="true"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     ),
     *     @OA\Response(
     *    response=401,
     *    description="softdeleted user",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, your account is disabled. Please contact your admin")
     *        )
     *     )
     *     )
     * )
     * )
     *
     */
    public function login(Request $request)
    {
     $validation= $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->deleted_at != Null) {
                return $this->ApiResponse(401, 'validation error', null);
            } else {
                $token = $user->createToken('token-name')->plainTextToken;
                return $this->ApiResponse(200, $token, null,$user);
            }
        }
             return $this->ApiResponse(403, 'Username or password is wrong', null);
        }

    /**
     * @OA\Post(
     * path="/api/register",
     * summary="register",
     * description="register by name , email, password",
     * operationId="authLogin",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Fill your Data",
     *    @OA\JsonContent(
     *       required={"name", "email","password"},
     *       @OA\Property(property="name", type="string", example="Ahmed"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     *
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $data = $request->all();
        $user = $this->create($data);
        return $this->ApiResponse(200, 'you have signed in', null);
    }

    public function create(array $data)
    {
        $Normaluser = Role::where('title','=','user')->first()->id;
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id'=>$Normaluser,
            'password' => Hash::make($data['password'])
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/logout",
     * summary="Logout",
     * description="Logout user and invalidate token",
     * operationId="authLogout",
     * tags={"auth"},
     * security={ {"sanctum": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success"
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not authorized"),
     *    )
     * )
     * )
     */
    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return $this->ApiResponse(200, 'USer logged out', null);
    }


    /**
     * @OA\Post(
     *     path="/api/profile",
     *     tags={"auth"},
     *     summary="return profile of user",
     *     operationId="Profile",
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *     )
     *     ),
     *     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not authorized"),
     *       )
     *     )
     *)
     *
     */
public function Profile(Request $request)
    {
        $user = auth('sanctum')->user();
        if ($user)
            return $this->ApiResponse(200, 'user data', null,$user);
        else
            return $this->ApiResponse(401, 'Unauthinticated user', null);
    }

    /**
     * @OA\Post(
     *     path="/api/profileEdit",
     *     tags={"auth"},
     *     summary="Edit profile of user",
     *     operationId="Profile",
     *     security={ {"sanctum": {} }},
     *      @OA\RequestBody(
     *    required=true,
     *    description="Fill your Data",
     *    @OA\JsonContent(
     *       required={"name", "email","password"},
     *       @OA\Property(property="name", type="string", example="Ahmed"),
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    )
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *     )
     *     )
     * )
     *
     *
     */

    public function profileEdit(Request $request)
    {
        $user = auth('sanctum')->user();

        $validator= $request->validate([
            'name' => 'unique:users,name,'.$request->id,
            'email' => 'email|unique:users,email'.$request->id,
            'password' => 'min:6',
        ]);

        if ($user) {
            $data=$request->all();
               $user->save($data);
            return $this->ApiResponse(200, 'user updated successfully', null);
        }
        else
            return $this->ApiResponse(403, 'Unauthinticated user', null);
    }
}
