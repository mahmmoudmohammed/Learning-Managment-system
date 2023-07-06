<?php

namespace App\Http\Controllers\Api\Modules\Questions;

use Exception;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\QuestionRequest;
use App\Http\Controllers\Api\BaseController;

class QuestionAPIController extends BaseController
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     * path="/api/questions/create",
     * summary="new topic",
     * description="store new question",
     * operationId="create",
     * tags={"Question"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass new question",
     *    @OA\JsonContent(
     *       required={"question","topic_id","difficulty"},
     *       @OA\Property(property="question", type="string", format="string", example="why.....?"),
     *       @OA\Property(property="topic_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="difficulty", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="question created")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid question name")
     *        )
     *     ),
     *@OA\Response(
     *    response=500,
     *    description="unknwon error",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="error when create question")
     *        )
     *     )
     * )
     *
     */
    public function store(QuestionRequest $request)
    {
        try {
            $question = Question::create($request->all());
            if ($question) {
                return $this->ApiResponse(200, "question created successfully");
            }
            return $this->ApiResponse(500, null, "error when create question");
        } catch (Exception $e) {
            return $this->ApiResponse(500, null, "internal server error " . $e);
        }
    }

    /**
     * @OA\Get(
     * path="/api/questions",
     * summary="Get All question",
     * description="Get All question",
     * operationId="",
     * tags={"Question"},
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="question", type="object", ref="#/components/schemas/Question"),
     *     )
     *  ),
     *@OA\Response(
     *     response=205,
     *     description="invalid ",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="no question found")
     *     )
     *  ),
     * )
     *
     */
    public function index()
    {
        $question = Question::all();
        if ($question) {
            return $this->ApiResponse(200, 'all Questions', null, $question);
        }
        return $this->ApiResponse(205, 'all Roles', 'No question Found', null);
    }

    /**
     * @OA\Get(
     * path="/api/questions/{id}",
     * summary="get question by id",
     * description="get question by id",
     * operationId="",
     * tags={"Question"},
     * @OA\Parameter(
     *          name="id",
     *          description="Question id",
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
     *        @OA\Property(property="question", type="object", ref="#/components/schemas/Question"),
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid question Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid question Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=205,
     *    description="You have No question to show",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No question to show")
     *        )
     *     )
     * )
     *
     */
    public function show(Question $question)
    {
        $question::with('answers');
        if ($question) {
            return $this->ApiResponse(200, null, null, $question);
        }
        return $this->ApiResponse(205, null, 'question not found', null);
    }

    /**
     * @OA\Post(
     * path="/api/questions/{id}/edit",
     * summary="Update question By Id",
     * description="Update question By Id",
     * operationId="",
     * tags={"Question"},
     * @OA\Parameter(
     *          name="id",
     *          description="Question id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *    description="valid question title",
     *    @OA\JsonContent(
     *       required={"question","topic_id","difficulty"},
     *       @OA\Property(property="question", type="string", example="what's you'r name?"),
     *       @OA\Property(property="topic_id", type="integer", example="1"),
     *       @OA\Property(property="difficulty", type="integer", example="1")
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="role", type="object", ref="#/components/schemas/Question"),
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid question Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid question Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=400,
     *    description="You have No question to update",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No question to update")
     *        )
     *     )
     * )
     *
     */
    public function update(QuestionRequest $request, Question $question)
    {
        $update = $question->update($request->all());
        if ($update) {
            return $this->ApiResponse(200, null, 'question updated successfully', null);
        }
        return $this->ApiResponse(400, null, 'an error when update question', null);
    }

    /**
     * @OA\Post(
     * path="/api/questions/{id}/delete",
     * summary="Delete question By Id",
     * description="Delete question By Id",
     * operationId="",
     * tags={"Question"},
     * @OA\Parameter(
     *          name="id",
     *          description="Question id",
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
     *       @OA\Property(property="message", type="string", example="question deleted successful")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid question Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid question Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=400,
     *    description="You have No question to delete",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No roles to delete")
     *        )
     *     )
     * )
     *
     */
    public function destroy(Question $question)
    {
        $delete = $question->delete();
        if ($delete) {
            return $this->ApiResponse(200, null, 'question deleted successfully', null);
        }
        return $this->ApiResponse(400, null, 'an error when delete question', null);
    }
}
