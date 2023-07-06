<?php

namespace App\Http\Controllers\Api\Modules\Answers;

use App\Http\Requests\AnswerRequest;
use App\Http\Traits\AuthTrait;
use Exception;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\ApiResponseTrait;

class AnswerAPIController extends BaseController
{
    use ApiResponseTrait, AuthTrait;

    /**
     * @OA\Post(
     * path="/api/answers/create",
     * summary="new answer",
     * description="store new answer",
     * operationId="create",
     * tags={"Answer"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass new answer",
     *    @OA\JsonContent(
     *       required={"answer","question_id","is_correct"},
     *       @OA\Property(property="answer", type="string", format="string", example="Iam fine"),
     *       @OA\Property(property="question_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="is_correct", type="integer", format="integer", example="1"),
     *    )
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="answer created")
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid input",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="Sorry, invalid answer name")
     *        )
     *     ),
     * @OA\Response(
     *    response=403,
     *    description="unauthrize",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="unauthrize")
     *        )
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="unauthinticate",
     *    @OA\JsonContent(
     *       @OA\Property(property="validation error", type="string", example="unauthinticate")
     *        )
     *     )
     * )
     *
     */
    public function store(AnswerRequest $request)
    {
        if (!$this->authUser('create-answer')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        try {
            $answer = Answer::create($request->all());
            if ($answer) {
                return $this->ApiResponse(200, "answer created successfully");
            }
        } catch (Exception $e) {
            return $this->ApiResponse(403, 'create answer unauthorized');
        }
    }

    /**
     * @OA\Get(
     * path="/api/answers",
     * summary="Get All answers",
     * description="Get All answers",
     * operationId="",
     * tags={"Answer"},
     * security={ {"sanctum": {} }},
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="question", type="object", ref="#/components/schemas/Answer"),
     *     )
     *  ),
     *@OA\Response(
     *     response=205,
     *     description="invalid ",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="no answer found")
     *     )
     *  ),
     * @OA\Response(
     *    response=401,
     *    description="unauthinticate",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="unauthinticate")
     *        )
     *     ),
     * @OA\Response(
     *    response=403,
     *    description="unauthrize",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="unauthrize")
     *        )
     *     ),
     * )
     *
     */
    public function index()
    {
        if (!$this->authUser('index-answer')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $answer = Answer::all();
        if ($answer) {
            return $this->ApiResponse(200, 'all answers', null, $answer);
        }
        return $this->ApiResponse(205, null, 'No answer Found', null);
    }

    /**
     * @OA\Get(
     * path="/api/answers/{id}",
     * summary="get answer by id",
     * description="get answer by id",
     * operationId="",
     * tags={"Answer"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *          name="id",
     *          description="Answer id",
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
     *        @OA\Property(property="answer", type="object", ref="#/components/schemas/Answer"),
     *     )
     *  ),
     * @OA\Response(
     *    response=404,
     *    description="invalid answer Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid answer Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=403,
     *    description="unauthrize",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="unauthrize")
     *        )
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="unauthrize",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="unauthinticate")
     *        )
     *     ),
     * )
     *
     */
    public function show(Answer $answerId)
    {
        if (!$this->authUser('show-answer')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        return $this->ApiResponse(200, null, null, $answerId);
    }

    /**
     * @OA\Post(
     * path="/api/answers/{id}/edit",
     * summary="Update answer By Id",
     * description="Update answer By Id",
     * operationId="",
     * tags={"Answer"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *          name="id",
     *          description="Answer id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *    description="valid answer title",
     *    @OA\JsonContent(
     *       required={"answer","question_id","is_correct"},
     *       @OA\Property(property="answer", type="string", example="it's good"),
     *       @OA\Property(property="question_id", type="integer", example="1"),
     *       @OA\Property(property="is_correct", type="integer", example="1")
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="role", type="object", ref="#/components/schemas/Answer"),
     *     )
     *  ),
     * @OA\Response(
     *    response=422,
     *    description="invalid answer Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid answer Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=404,
     *    description="You have No answer to update",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No answer to update")
     *        )
     *     ),
     * @OA\Response(
     *    response=401,
     *    description="unauthinticate",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="unauthinticate")
     *        )
     *     ),
     * @OA\Response(
     *    response=403,
     *    description="unauthrize",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="unauthrize")
     *        )
     *     ),
     * )
     *
     */
    public function update(AnswerRequest $request, Answer $answerId)
    {
        if (!$this->authUser('update-answer')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $update = $answerId->update($request->all());
        if ($update) {
            return $this->ApiResponse(200, 'answer updated successfully');
        }
        return $this->ApiResponse(400, 'an error when update answer');
    }

    /**
     * @OA\Post(
     * path="/api/answers/{id}/delete",
     * summary="Delete answer By Id",
     * description="Delete answer By Id",
     * operationId="",
     * tags={"Answer"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *          name="id",
     *          description="Answer id",
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
     *       @OA\Property(property="message", type="string", example="answer deleted successful")
     *     )
     *  ),
     * @OA\Response(
     *    response=404,
     *    description="invalid answer Id",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="invalid answer Id")
     *        )
     *     ),
     * @OA\Response(
     *    response=400,
     *    description="You have No answer to delete",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="You have No answer to delete")
     *        )
     *     )
     * )
     *
     */
    public function destroy(Answer $answerId)
    {
        if (!$this->authUser('delete-answer')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $delete = $answerId->delete();
        if ($delete) {
            return $this->ApiResponse(200, 'answer deleted successfully', null);
        }
        return $this->ApiResponse(400, 'an error when delete answer', null);
    }
}
