<?php

namespace App\Http\Controllers\Api\Modules\Permissions;

use App\Http\Traits\AuthTrait;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Controllers\Api\Modules\Permissions\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PermissionAPIController extends BaseController
{
    use ApiResponseTrait,AuthTrait;

    /**
     * @OA\Post(
     * path="/api/permission/create",
     * summary="new permmision",
     * description="store new permission",
     * operationId="create",
     * tags={"permission"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass new permission name",
     *    @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", format="string", example="create admin"),

     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="permission created")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid input")
     *        )
     *     )
     * )
     *
     */
    public function create(Request $request)
    {
        if (!$this->authUser('create-permission')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $validation = Validator::make(
            $request->all(),
            ['name' => 'required|unique:permissions,name',]
        );

        if ($validation->fails()) {
            return $this->ApiResponse(422, 'validation error', $validation->errors());
        }
        try {
            $permission =  Permission::create(['name' => $request->name,]);
            return $this->ApiResponse(200, 'permission created', null, $permission);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'some bugs call the develper');
        }
    }

    /**
     * @OA\Post(
     * path="/api/permission/update",
     * summary="update permmision",
     * description="update permission",
     * operationId="update",
     * tags={"permission"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="update permission name",
     *    @OA\JsonContent(
     *       required={"permission new name"},
     *       @OA\Property(property="name", type="string", format="string", example="update admin"),
     *  required={"permission_id"},
     *       @OA\Property(property="permission_id", type="number", format="number", example="10"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="permission updated")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid input")
     *        )
     *     )
     * )
     *
     */
    public function update(Request $request)
    {
        if (!$this->authUser('update-permission')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $validation = Validator::make($request->all(), [
            'permission_id' => 'required|exists:permissions,id',
            'name' => [
                'required',
                Rule::unique('permissions')->ignore($request->permission_id)
            ],
        ]);

        if ($validation->fails()) {
            return $this->ApiResponse(421, 'validation error', $validation->errors());
        }
        try {
            $permission = Permission::find($request->permission_id);
            if (!$permission) {
                return $this->ApiResponse(404, 'permission not found');
            }
            $permission->update(['name' => $request->name]);
            return $this->ApiResponse(200, 'permission updated', null, $permission);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'some bugs call the develper');
        }
    }

    /**
     * @OA\Post(
     * path="/api/permission/view",
     * summary="view permmision",
     * description="view permission",
     * operationId="view",
     * tags={"permission"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="return permission ",
     *    @OA\JsonContent(

     *  required={"permission_id"},
     *       @OA\Property(property="permission_id", type="number", format="number", example="10"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="permission")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid permission_id")
     *        )
     *     )
     * )
     *
     */
    public function view(Request $request)
    {
        if (!$this->authUser('show-permission')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $validation = Validator::make($request->all(), ['permission_id' => 'required|exists:permissions,id']);

        if ($validation->fails()) {
            return $this->ApiResponse(421, 'validation error', $validation->errors());
            // return response(['message' => 'validation error', $validation->errors()], 422);
        }
        try {
            $permission = Permission::find($request->permission_id);
            if (!$permission) {
                return $this->ApiResponse(404, 'permission not found');
            }
            return $this->ApiResponse(200, 'permission updated', null, $permission);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'some bugs call the develper');
        }
    }

    /**
     * @OA\Post(
     * path="/api/permission/delete",
     * summary="delete permmision",
     * description="delete permission",
     * operationId="delete",
     * tags={"permission"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="return permission ",
     *    @OA\JsonContent(

     *  required={"permission_id"},
     *       @OA\Property(property="permission_id", type="number", format="number", example="10"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="permission deleted")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid permission_id")
     *        )
     *     )
     * )
     *
     */
    public function delete(Request $request)
    {
        if (!$this->authUser('delete-permission')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $validation = Validator::make($request->all(), ['permission_id' => 'required|exists:permissions,id']);

        if ($validation->fails()) {
            return $this->ApiResponse(421, 'validation error', $validation->errors());
        }
        try {
            $permission = Permission::find($request->permission_id);

            if (!$permission) {
                return $this->ApiResponse(404, 'permission not found');
            }
            $permission->delete();
            return $this->ApiResponse(200, 'permission deleted', null, $permission);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'some bugs call the develper');
        }
    }

    /**
     * @OA\Post(
     * path="/api/permission/index",
     * summary="all permmision",
     * description="all permissions",
     * operationId="all",
     * tags={"permission"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=false,
     *    description="",
     *    @OA\JsonContent(
     *       required={""},
     *       @OA\Property(property="", type="", format="", example=""),

     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="all permissions")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid input")
     *        )
     *     )
     * )
     *
     */
    public function index()
    {
        if (!$this->authUser('index-permission')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $permissions = Permission::all();
        if ($permissions) {
            return $this->ApiResponse(200, 'all permissions', null, $permissions);
        }
        return $this->ApiResponse(400, 'no permissions found');
    }
}
