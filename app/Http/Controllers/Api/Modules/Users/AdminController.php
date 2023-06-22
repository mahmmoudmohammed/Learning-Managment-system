<?php

namespace App\Http\Controllers\Api\Modules\Users;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\Modules\Users\User;
use App\Http\Controllers\Api\Modules\Roles\Role;
use App\Http\Traits\ApiResponseTrait;
use http\Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use phpDocumentor\Reflection\Types\Null_;

class AdminController extends BaseController
{
    use ApiResponseTrait;
    /**
     * @OA\Get(
     *      path="/api/admins",
     *      operationId="getAdminsList",
     *      tags={"Admin"},
     *      summary="Get list of Admins",
     *      description="Returns list of Admin Users Data",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="admins", type="object", ref="#/components/schemas/User"),
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
         try {
             $admins = User::whereHas('role', function($query){
                 $query->where('title', 'like', User::adminUser);
             })->get();
             return $this->ApiResponse(200, 'message',Null,$admins);
         } catch (Exception $e) {
             return $this->ApiResponse(500, 'No data provided');
         }
     }

    /**
     * @OA\Post(
     * path="/api/admins",
     * summary="create admin",
     * description="create new admin user",
     * operationId="create Admin",
     * tags={"Admin"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass admin data",
     *    @OA\JsonContent(
     *       required={"name","email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="name", type="string", example="user1"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *        )
     * ),
     * @OA\Response(
     *     response=201,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="success", type="string", example="create new admin successfully"),
     *        @OA\Property(property="admin", type="object", ref="#/components/schemas/User"),
     *     )
     *  ),
     * @OA\Response(
     *    response=400,
     *    description="can not Add admin try later",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Sorry, wrong email address or data incomplete. Please try again")
     *        )
     *     )
     * )
     *
     */
    public function store(Request $request)
    {
        $request->validate([
                'name'=>  ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6'],
            ]
        );
        $data = $request->all();
        $role = Role::where('title','=',User::adminUser)->first();
        $admin = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $role->id
        ]);
        if(!$admin)
        {
            return $this->ApiResponse(400, null,'can not Add Admin try later',$admin);
        }
        return $this->ApiResponse(200,'User Created Successfully',null,$admin);
    }

    /**
     * @OA\Get(
     *      path="/api/admins/{id}",
     *      operationId="getAdmin",
     *      tags={"Admin"},
     *      summary="Get Admin profile",
     *      description="Returns Admins profile Data",
     *     @OA\Parameter(
     *          name="id",
     *          description="Admin id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="admin", type="object", ref="#/components/schemas/User"),
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function show($id)
    {
        $admin = User::find($id);
        if (!is_null($admin))
        {
            return $this->ApiResponse(200,null,null,$admin);
        }
        return $this->ApiResponse(500, 'can not Find Admin Data');
    }

    /**
     * @OA\Put(
     *      path="/api/admins/{id}",
     *      operationId="updateAdmin",
     *      tags={"Admin"},
     *      summary="Update existing Admin ",
     *      description="Update existing Admin Data name , email",
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="id",
     *          description="id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Update Admin Data",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Ahmed"),
     *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),)
     *      ),
     *      @OA\Response(
     *          response=202,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="string", example="Profile Updated successfully")
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Profile can not be Updated.")
     *          )
     *      )
     * )
     */

    public function update(Request $request,$id)
    {
        $auth = auth('sanctum')->user();
        if (!$auth) {
            return $this->ApiResponse(401,null,'You are not authorize to update this Profile',);
        }
        $admin = User::find($id);
        if (!$admin) {
            return $this->ApiResponse(401,null,'Profile doesn\'t exist');
        }
        $admin = $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if (!$admin) {
            return $this->ApiResponse(400,null,' something error try again later');
        }
        return $this->ApiResponse(200,'Profile Updated successfully',null);
    }

    /**
     * @OA\Delete(
     *      path="/api/admins/{id}",
     *      operationId="destroy",
     *      tags={"Admin"},
     *      summary="Delete existing Admin",
     *      description="Deletes a Admin and returns no Message",
     *      @OA\Parameter(
     *          name="id",
     *          description="user id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="string", example="Admin Moved to trash")
     *           )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="User Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Admin Not Found..")
     *          )
     *     )
     * )
     *
     */
    public function destroy($id)
    {
        $admin = User::find($id);

        if (!$admin) {
            return $this->ApiResponse(400, null, 'THIS ACCOUNT NOT EXIST.');
        }
        $admin->delete();
        return $this->ApiResponse(301, 'account Moved to trash...' );
    }
}
