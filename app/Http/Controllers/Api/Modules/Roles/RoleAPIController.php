<?php

namespace App\Http\Controllers\Api\Modules\Roles;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Traits\ApiResponseTrait;

class RoleAPIController extends BaseController
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     * path="/api/roles/create",
     * summary="create role",
     * description="crearte new role",
     * operationId="",
     * tags={"Role"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="valid role title & permission id",
     *    @OA\JsonContent(
     *       required={"title,permissions"},
     *       @OA\Property(property="title", type="string", example="admin"),
     *        @OA\Property(
     *      property="permissions",
     *      type="array",
     *       @OA\Items(
     *               type="number",
     *               description="The permission ID",
     *               @OA\Schema(type="number")
     *         ),
     *     example="[1,2,3]",
     * ),
     * ),
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="role", type="object", ref="#/components/schemas/Role"),
     *     )
     *  ),
     * @OA\Response(
     *    response=400,
     *    description="role title already exist",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong Role title, Role title already exist")
     *        )
     *     )
     * )
     *
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create([
            'title' => $request->title
        ]);
        if ($role) {
            $role->permissions()->Sync($request->permissions);
            return $this->ApiResponse(200, 'Role Created successfuly',
                null, null);
        }
        return $this->ApiResponse(400, null, 'An Erron when create Role', null);
    }

    /**
     * @OA\Get(
     * path="/api/roles",
     * summary="Get All Role",
     * description="Get All Role",
     * operationId="",
     * tags={"Role"},
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="role", type="object", ref="#/components/schemas/Role"),
     *       @OA\Property(
     *      property="permissions",
     *      type="array",
     *       @OA\Items(
     *               type="number",
     *               description="The permission ID",
     *               @OA\Schema(type="number")
     *         ),
     *     example="['permiision_id'=>1]",
     *      ),
     *    ),
     *    )
     *  ),
     * @OA\Response(
     *     response=205,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No roles to show")
     *     )
     *  ),
     * )
     *
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        if ($roles) {
            $roles = RoleResource::collection($roles);
            return $this->ApiResponse(200, 'all Roles', null, $roles);
        }
        return $this->ApiResponse(205, 'all Roles', 'No Role Found', null);
    }

    /**
     * @OA\Get(
     * path="/api/roles/{id}",
     * summary="get role by id",
     * description="get role by id",
     * operationId="",
     * tags={"Role"},
     * @OA\Parameter(
     *          name="id",
     *          description="Role id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="role", type="object", ref="#/components/schemas/Role"),
     *        @OA\Property(
     *               property="permissions",
     *               type="array",
     *               @OA\Items(
     *                  type="number",
     *                  description="The permission ID",
     *                  @OA\Schema(type="number")
     *                ),
     *              example="['permiision_id'=>1]",
     *       ),
     *      ),
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid Role Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid Role Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=205,
     *    description="You have No roles to show",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No roles to show")
     *        )
     *     )
     * )
     *
     */
    public function show(Role $role)
    {
        $role::with('permissions');
        if ($role) {
            $role = new RoleResource($role);
            return $this->ApiResponse(200, null, null, $role);
        }
        return $this->ApiResponse(205, null, 'role not found', null);
    }

    /**
     * @OA\Post(
     * path="/api/roles/{id}/edit",
     * summary="Update Role By Id",
     * description="Update Role By Id",
     * operationId="",
     * tags={"Role"},
     * @OA\Parameter(
     *          name="id",
     *          description="Role id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *    description="valid role title",
     *    @OA\JsonContent(
     *       required={"title","permissions"},
     *       @OA\Property(property="title", type="string", example="admin"),
     *       @OA\Property(
     *      property="permissions",
     *      type="array",
     *       @OA\Items(
     *               type="number",
     *               description="The permission ID",
     *               @OA\Schema(type="number")
     *         ),
     *     example="[1,2,3]",
     *      ),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="role", type="object", ref="#/components/schemas/Role"),
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid Role Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid Role Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=205,
     *    description="You have No roles to show",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No roles to show")
     *        )
     *     )
     * )
     *
     */
    public function update(RoleRequest $request, Role $role)
    {

        $role::with('permissions');
        $role->permissions()->sync($request->permissions);
        $update = $role->update(['title' => $request->title]);
        if ($update) {
            return $this->ApiResponse(200, null, 'role updated successfully', null);
        }
        return $this->ApiResponse(205, null, 'an error when update Role', null);
    }

    /**
     * @OA\Post(
     * path="/api/roles/{id}/delete",
     * summary="Delete Role By Id",
     * description="Delete Role By Id",
     * operationId="",
     * tags={"Role"},
     *     @OA\Parameter(
     *          name="id",
     *          description="Role id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Role deleted successful")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid Role Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid Role Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=205,
     *    description="You have No roles to show",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No roles to delete")
     *        )
     *     )
     * )
     *
     */
    public function destroy(Role $role)
    {
        $delete = $role->delete();
        if ($delete) {
            return $this->ApiResponse(200, null, 'role deleted successfully', null);
        }
        return $this->ApiResponse(205, null, 'an error when delete Role', null);
    }
}
