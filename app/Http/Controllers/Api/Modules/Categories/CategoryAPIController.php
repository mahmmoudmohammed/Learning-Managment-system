<?php

namespace App\Http\Controllers\Api\Modules\Categories;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\Modules\Categories\Category;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Traits\AuthTrait;

class CategoryAPIController extends BaseController
{
    use ApiResponseTrait,AuthTrait;

    /**
     * @OA\Get(
     *      path="/api/categories",
     *      operationId="getCategoriesList",
     *      tags={"Category"},
     *      summary="Get list of Categories",
     *      description="Returns list of categories Data",
     *      security={ {"sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="categories", type="object", ref="#/components/schemas/Category"),
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
            if(!$this->authUser('index-category'))
            {
                return $this->ApiResponse(403,'This action is unauthized',Null);
            }
            $categories = Category::all();
            return $this->ApiResponse(Response::HTTP_OK, null,Null,$categories);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'No provided data '.$e);
        }
    }

    /**
     * @OA\Post(
     * path="/api/categories",
     * summary="new category",
     * description="store new category",
     * operationId="create",
     * tags={"Category"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass new category name",
     *    @OA\JsonContent(
     *       required={"name","parent_id"},
     *       @OA\Property(property="name", type="string", format="string", example="Backend"),
     *       @OA\Property(property="parent_id", type="integer", format="integer", example="null"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="category created")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="category can't be created try later")
     *        )
     *     )
     * )
     *
     */
    public function store(Request $request)
    {
        if(!$this->authUser('create-category'))
        {
            return $this->ApiResponse(403,'This action is unauthized',Null);
        }
        $request->validate( [
            'name' => 'required|unique:categories',
            'parent_id' => 'nullable|integer|exists:categories,id',
            ]);
        try {
            $project = Category::create($request->all());
            return $this->ApiResponse(Response::HTTP_CREATED, "category created successfully",null, $project);
        }catch (Exception $e) {
            return $this->ApiResponse(500, 'category can\'t be created try later');
        }
    }

    /**
     * @OA\Get(
     *      path="/api/categories/{id}",
     *      operationId="getCategory",
     *      tags={"Category"},
     *      summary="Get specific Category ",
     *      description="Returns specific Category Data",
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="id",
     *          description="Category id",
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
     *              @OA\Property(property="Category", type="object", ref="#/components/schemas/Category"),
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
        if(!$this->authUser('show-category'))
        {
            return $this->ApiResponse(403,'This action is unauthized',Null);
        }
        $category = Category::find($id);
        if (!is_null($category))
        {
            return $this->ApiResponse(Response::HTTP_OK,null,null,$category);
        }
        return $this->ApiResponse(Response::HTTP_NOT_FOUND, 'can not Find category Data');
    }

    /**
     * @OA\Put (
     * path="/api/categories/{id}",
     * summary="update existing category",
     * description="update category",
     * operationId="updateCategory",
     * tags={"Category"},
     * security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="id",
     *          description="Category id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *    description="update category name",
     *    @OA\JsonContent(
     *       required={"name","parent_id"},
     *       @OA\Property(property="name", type="string", format="string", example="software"),
     *       @OA\Property(property="parent_id", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="category updated")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid category name")
     *        )
     *     )
     * )
     *
     */
    public function update(Request $request,$id)
    {
        if(!$this->authUser('update-category'))
        {
            return $this->ApiResponse(403,'This action is unauthized',Null);
        }
        $request->validate([
                'parent_id' => 'required|nullable|exists:categories,id',
                'name' => 'required'
                ]);
        try {
            $category = Category::find($id);
            if (!$category) {
                return $this->ApiResponse(Response::HTTP_NOT_FOUND, 'category not found');
            }
            $category->update($request->all());
            return $this->ApiResponse(Response::HTTP_ACCEPTED, 'category updated', null, $category);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'Update process can not be complete, try later');
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/categories/{id}",
     *      operationId="destroy",
     *      tags={"Category"},
     *      summary="Delete existing Category",
     *      description="Deletes a Category and returns Message",
     *     security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="Category id",
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
     *              @OA\Property(property="success", type="string", example="Category Moved to trash")
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
        if(!$this->authUser('delete-category'))
        {
            return $this->ApiResponse(403,'This action is unauthized',Null);
        }
        $category = Category::find($id);

        if (!$category) {
            return $this->ApiResponse(Response::HTTP_NOT_FOUND, null, 'THIS CATEGORY NOT EXIST.');
        }
        $category->delete();
        return $this->ApiResponse(Response::HTTP_MOVED_PERMANENTLY, 'Category Moved to trash...' );
    }
}
