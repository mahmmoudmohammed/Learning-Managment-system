<?php

namespace App\Http\Controllers\Api\Modules\Topics;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\ApiResponseTrait;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TopicAPIController extends BaseController
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *      path="/api/topics",
     *      operationId="index",
     *      tags={"Topic"},
     *      summary="Get list of topics",
     *      description="Returns list of topics Data",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="topics", type="object", ref="#/components/schemas/Topic"),
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
            $topic = Topic::all();
            return $this->ApiResponse(Response::HTTP_OK, null,Null,$topic);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'No provided data ');
        }
    }

    /**
     * @OA\Post(
     * path="/api/topics",
     * summary="new topic",
     * description="store new topic",
     * operationId="create",
     * tags={"Topic"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass new topic name",
     *    @OA\JsonContent(
     *       required={"title","category_id"},
     *       @OA\Property(property="title", type="string", format="string", example="laravel"),
     *       @OA\Property(property="category_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="status", type="boolean", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="topic created")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid topic name")
     *        )
     *     )
     * )
     *
     */
    public function store(Request $request)
    {
        $request->validate([
                'title' => 'required|unique:topics',
                'category_id' => 'required|integer|exists:categories,id',
            ]);
        try {
            $topic = Topic::create($request->all());
            return $this->ApiResponse(Response::HTTP_CREATED, "topic created successfully",null, $topic);
        }catch (Exception $e) {
            return $this->ApiResponse(500, 'topic can\'t be created try later');
        }
    }


    /**
     * @OA\Get(
     *      path="/api/topics/{id}",
     *      operationId="show",
     *      tags={"Topic"},
     *      summary="Get specific Topic ",
     *      description="Returns specific Topic Data",
     *     @OA\Parameter(
     *          name="id",
     *          description="Topic id",
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
     *              @OA\Property(property="Topic", type="object", ref="#/components/schemas/Topic"),
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
        $topic = Topic::find($id);
        if (!is_null($topic))
        {
            return $this->ApiResponse(Response::HTTP_OK,null,null,$topic);
        }
        return $this->ApiResponse(Response::HTTP_NOT_FOUND, 'can not Find topic Data');
    }

    /**
     * @OA\Put (
     * path="/api/topics/{id}",
     * summary="update existing topic",
     * description="update topic",
     * operationId="update",
     * tags={"Topic"},
     *     @OA\Parameter(
     *          name="id",
     *          description="topic id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *    description="update topic title",
     *    @OA\JsonContent(
     *       required={"title","category_id"},
     *       @OA\Property(property="title", type="string", format="string", example="PHP"),
     *       @OA\Property(property="status", type="boolean", example="0"),
     *       @OA\Property(property="category_id", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="topic updated")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid topic title")
     *        )
     *     )
     * )
     *
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required',
        ]);
        try {
            $topic = Topic::find($id);
            if (!$topic) {
                return $this->ApiResponse(Response::HTTP_NOT_FOUND, 'topic not found');
            }
            $topic->update($request->all());
            return $this->ApiResponse(Response::HTTP_ACCEPTED, 'topic updated', null, $topic);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'Update process can not be complete, try later');
        }
    }


    /**
     * @OA\Delete(
     *      path="/api/topics/{id}",
     *      operationId="destroy",
     *      tags={"Topic"},
     *      summary="Delete existing Topic",
     *      description="Deletes a Topic and returns Message",
     *      @OA\Parameter(
     *          name="id",
     *          description="Topic id",
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
     *              @OA\Property(property="success", type="string", example="Topic Moved to trash")
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
     *              @OA\Property(property="error", type="string", example="Topic Not Found..")
     *          )
     *     )
     * )
     *
     */
    public function destroy($id)
    {
        $topic = Topic::find($id);

        if (!$topic) {
            return $this->ApiResponse(Response::HTTP_NOT_FOUND, null, 'THIS TOPIC NOT EXIST.');
        }
        $topic->delete();
        return $this->ApiResponse(Response::HTTP_MOVED_PERMANENTLY, 'Topic Moved to trash...' );
    }
}
