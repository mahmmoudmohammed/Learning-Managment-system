<?php

namespace App\Http\Controllers\Api\Modules\UserCertificate;

use App\Http\Traits\AuthTrait;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Controllers\Api\Modules\UserCertificate\Certificate;
use App\Http\Controllers\Api\Modules\Users\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CertificateAPIController extends BaseController
{
    use ApiResponseTrait, AuthTrait;

    /**
     * @OA\Post(
     * path="/api/Certificate/create",
     * summary="new Certificate",
     * description="store new Certificate",
     * operationId="create",
     * tags={"Certificate"},
     *  security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass new certificate name and topic ID",
     *    @OA\JsonContent(
     *       required={"title","topic_id","level","number","duration"},
     *       @OA\Property(property="title", type="string", format="string", example="PHP advanced"),
     *       @OA\Property(property="topic_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="level", type="integer", format="integer", example="1"),
     *       @OA\Property(property="number", type="integer", format="integer", example="1"),
     *       @OA\Property(property="duration", type="integer", format="integer", example="1"),
     *       )
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *          @OA\Property(property="user", type="object", ref="#/components/schemas/Certificate"),
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
        if (!$this->authUser('create-certificate')) {
            return $this->ApiResponse(403, "unauthorized");
        }
        $validation = Validator::make($request->all(),
            ['title' => 'required|unique:certificates',
                'topic_id' => 'required|exists:topics,id'
            ]
        );

        if ($validation->fails()) {
            return $this->ApiResponse(422, 'validation error', $validation->errors());
        }
        try {
            $certificate = Certificate::create($request->all());

            return $this->ApiResponse(200, 'Certificate created', null, $certificate);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'some bugs call the develper');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/Certificate/Edit",
     *     tags={"Certificate"},
     *     summary="Edit Certificate data",
     *     operationId="certificate Edit",
     *     security={ {"sanctum": {} }},
     *      @OA\RequestBody(
     *    required=true,
     *    description="Certificate data",
     *    @OA\JsonContent(
     *       required={"certificate_id","title","topic_id","level","number","duration"},
     *       @OA\Property(property="title", type="string", format="string", example="PHP advanced"),
     *       @OA\Property(property="topic_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="certificate_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="level", type="integer", format="integer", example="1"),
     *       @OA\Property(property="number", type="integer", format="integer", example="1"),
     *       @OA\Property(property="duration", type="integer", format="integer", example="1"),
     *       )
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="certificate", type="object", ref="#/components/schemas/Certificate"),
     *     )
     *     )
     * )
     *
     *
     */
    public function Edit(Request $request)
    {
        if (!$this->authUser('update-certificate')) {
            return $this->ApiResponse(403, "unauthorized");
        }

        $validator = $request->validate([
            'title' => 'required|unique:certificates,title,' . $request->certificate_id,
            'topic_id' => 'required|numeric|unique:certificates,topic_id,' . $request->certificate_id,
            'certificate_id' => 'required|exists:certificates',
            'level' => 'required|numeric|unique:certificates,level,' . $request->certificate_id,
            'duration' => 'required|numeric|unique:certificates,duration,' . $request->certificate_id,
        ]);

        try {
            $certificate = Certificate::where('id', $request->certificate_id)->first();

            if ($certificate) {
                $data=$request->all();
                $certificate->save($data);
                return $this->ApiResponse(200, 'Certificate updated successfully', null);
            } else
                return $this->ApiResponse(204, 'Certificate not found', null);
        } catch (Exception $e) {
            return $e->getMessage();
            return $this->ApiResponse(500, 'some bugs call the develper');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/Certificate/Show",
     *     tags={"Certificate"},
     *     summary="show Certificate data",
     *     operationId="certificate view",
     *     security={ {"sanctum": {} }},
     *      @OA\RequestBody(
     *    required=true,
     *    description="Certificate data",
     *    @OA\JsonContent(
     *       required={"certificate_id"},
     *       @OA\Property(property="certificate_id", type="number", format="number", example="1"),
     *    )
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="certificate", type="object", ref="#/components/schemas/Certificate"),
     *     )
     *     )
     * )
     *
     *
     */
    public function Show(Request $request)
    {
        if (!$this->authUser('show-certificate')) {
            return $this->ApiResponse(403, "unauthorized");
        }

        $validator = $request->validate([
            'certificate_id' => 'required|exists:certificates,id',
        ]);

        try {
            $certificate = Certificate::where('id', $request->certificate_id)->first();

            if ($certificate) {
                return $this->ApiResponse(200, 'Certificate data', null, $certificate);
            } else
                return $this->ApiResponse(204, 'Certificate not found', null);
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'some bugs call the develper');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/Certificate/Delete",
     *     tags={"Certificate"},
     *     summary="Delete Certificate",
     *     operationId="certificate view",
     *     security={ {"sanctum": {} }},
     *      @OA\RequestBody(
     *    required=true,
     *    description="Certificate data",
     *    @OA\JsonContent(
     *       required={"certificate_id"},
     *       @OA\Property(property="certificate_id", type="number", format="number", example="1"),
     *    )
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="certificate", type="object", ref="#/components/schemas/Certificate"),
     *     )
     *     )
     * )
     *
     *
     */
    public function delete(Request $request)
    {
        if (!$this->authUser('delete-certificate')) {
            return $this->ApiResponse(403, "unauthorized");
        }
            $validator = $request->validate([
                'certificate_id' => 'required|exists:certificates:id',
            ]);

            try {
                $certificate = Certificate::where('id', $request->certificate_id)->first();

                if ($certificate) {
                    $certificate->delete();
                    return $this->ApiResponse(200, 'Certificate deleted', null, $certificate);
                } else
                    return $this->ApiResponse(204, 'Certificate not found', null);
            } catch (Exception $e) {
                return $this->ApiResponse(500, 'some bugs call the develper');
            }
    }

    /**
     * @OA\Post(
     *     path="/api/Certificate/Index",
     *     tags={"Certificate"},
     *     summary="all Certificate of topic",
     *     operationId="certificate view",
     *     security={ {"sanctum": {} }},
     *      @OA\RequestBody(
     *    required=true,
     *    description="Certificate data",
     *    @OA\JsonContent(
     *       required={"topic_id"},
     *       @OA\Property(property="topic_id", type="number", format="number", example="1"),
     *    )
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="certificate", type="object", ref="#/components/schemas/Certificate"),
     *     )
     *     )
     * )
     *
     *
     */
    public function index(Request $request)
    {
        if (!$this->authUser('index-certificate')) {
            return $this->ApiResponse(403, "unauthorized");
        }

            $validator = $request->validate([
                'topic_id' => 'required|exists:topics,id',
            ]);

            try {
                $certificate = Certificate::where('topic_id', $request->topic_id)->get();

                if ($certificate) {
                    return $this->ApiResponse(200, 'Certificates', null, $certificate);
                } else
                    return $this->ApiResponse(204, 'No Certificates found', null);
            } catch (Exception $e) {
                return $this->ApiResponse(500, 'some bugs call the develper');
            }
    }
}
