<?php

namespace App\Http\Controllers\Api\Modules\UserCertificate;

use App\Http\Controllers\Api\Modules\Answers\Answer;
use App\Http\Controllers\Api\Modules\Questions\Question;
use App\Http\Controllers\Api\Modules\UserCertificate\Certificate;
use App\Http\Resources\GetQuestionResource;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Controllers\Api\Modules\Users\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserCertificatesAPIController extends BaseController
{
    use ApiResponseTrait;

    /**
     * @OA\Post(
     * path="/api/UserCertificate/start",
     * summary="new Certificate",
     * description="store new Certificate",
     * operationId="create",
     * tags={"UserCertificate"},
     *  security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass new certificate name and topic ID",
     *    @OA\JsonContent(
     *       required={"certificate_id"},
     *       @OA\Property(property="certificate_id", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *          @OA\Property(property="user", type="string", example="Certificate started"),
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
    public function startCertificate(Request $request)
    {
        //validate on user id
        $user = auth('sanctum')->user();

        //validate on certificate id
        $validation = Validator::make($request->all(), ['certificate_id' => 'required|exists:certificates,id']);

        try {
            //store input into array
            $data['user_id'] = $user->id;
            $data['certificate_id'] = $request->certificate_id;
            $data['credinitial'] = Str::random(20);

            //strores data of certificate
            $this->storeCertificate($data);

            //retreive Questions by level of certificate
            $this->getQuestion($request->certificate_id, $user->id);

            //get the questions from cache and get the first question
            $cachedquestion  = Redis::get('question' . $user->id);
            $questions = json_decode($cachedquestion, FALSE);

            return  $this->ApiResponse(200, null, null, $this->fetchQuestion($questions, 0));
        } catch (Exception $e) {
            return $this->ApiResponse(500, 'some bugs call the develper');
        }
    }

    //get the question by its index
    function fetchQuestion($question, $index)
    {
        return $question[$index];
    }

    //get the question index by its id
    function indexQuestion($question, $val)
    {
        for ($i = 0; $i < count($question); $i++) {
            if ($question[$i]->id == $val) {
                return $i;
            }
        }
    }

    //save the data
   function storeCertificate($data)
    {
        $user = auth('sanctum')->user();
        $pivot = [
            'certificate_id' => $data['certificate_id'],
            'credintials' => "N/A",
            'completed' => 0,
            'result' => 0,
            'created_at' => Carbon::now()
        ];

        return $user->certificates()->attach($user->id, $pivot);
    }

    //get questions of this certificate
    function getQuestion($id, $user_id)
    {
        //get certificate model by its id
        $certificate = Certificate::where('id', $id)->first();

        //check the level of certificate
        if ($certificate->level != 0) {
            $questions = Question::with('answers')->inRandomOrder()->limit($certificate->number)
                ->where(['topic_id' => $certificate->topic_id, 'difficulty' => $certificate->level])
                ->get();
            $question = GetQuestionResource::collection($questions);
        } else {
            $part = floor($certificate->number / 3);
            $diff = $certificate->number % 3;
            $easy = Question::with('answers')->inRandomOrder()->limit($part + $diff)
                ->where(['topic_id' => $certificate->topic_id, 'difficulty' => 1])
                ->get();

            $meduim = Question::with('answers')->inRandomOrder()->limit($part)
                ->where(['topic_id' => $certificate->topic_id, 'difficulty' => 2])
                ->get();

            $hard = Question::with('answers')->inRandomOrder()->limit($part)
                ->where(['topic_id' => $certificate->topic_id, 'difficulty' => 3])
                ->get();

            $questions = $easy->merge($meduim)->merge($hard);
        }
        $question = GetQuestionResource::collection($questions);
        $encode = json_encode($question);
        Redis::set('question' . $user_id, $encode);
    }

    /**
     * @OA\Post(
     * path="/api/UserCertificate/navigate",
     * summary="questionNevigator",
     * description="question Nevigator ",
     * operationId="create",
     * tags={"UserCertificate"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass new certificate name and topic ID",
     *    @OA\JsonContent(
     *       required={"action","question_id"},
     *       @OA\Property(property="action", type="string", format="string", example="next"),
     *       @OA\Property(property="question_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="answer_id", type="integer", format="integer", example="1"),
     *       )
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *          @OA\Property(property="user", type="string", example="Next question"),
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
    public function questionNevigator(Request $request)
    {
        $user = auth('sanctum')->user();

        $validation = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'answer_id' => 'exists:answers,id',
            'action' => 'in next,previous'
        ]);

        $cachedQuestions = Redis::get('question' . $user->id);
        $questions = json_decode($cachedQuestions, false);

        $index = $this->indexQuestion($questions, $request->question_id);

        $thisQuestion = $this->fetchQuestion($questions, $index);
        $thisQuestion->userAnswer = (isset($request->answer_id)) ? $request->answer_id : null;
        $encode = json_encode($questions);
        $cachedQuestions = Redis::set('question' . $user->id, $encode);

        if ($request->action == 'next') {
            if ($index != count($questions) - 1) {
                return $this->fetchQuestion($questions, $index + 1);
            } else {
                return $this->ApiResponse('201', "Last question");
            }
        } else {
            if ($index != 0) {
                return $this->fetchQuestion($questions, $index - 1);
            } else {
                return $this->ApiResponse('201', "Fisrt question");
            }
        }
    }

    function storeAnswers($user_id, $certifite_id)
    {
        $cachedquestions = Redis::get('question' . $user_id);
        $questions = json_decode($cachedquestions, false);

        //get answers
        for ($i = 0; $i < count($questions); $i++) {
            $correct = ($questions[$i]->userAnswer != null) ?
                Answer::select('is_correct')->where('id', $questions[$i]->userAnswer)->first()->is_correct : 0;

            $answer = CertificateAnswer::create([
                'user_id' => $user_id,
                'question_id' => $questions[$i]->id,
                'answer_id' => (isset($questions[$i]->userAnswer)) ? $questions[$i]->userAnswer : null,
                'certificate_id' => $certifite_id,
                'is_correct' => $correct
            ]);

            Redis::del('question' . $user_id);
        }
    }

    function calculateResult($user_id, $certificate_id)
    {
        $total = Certificate::select('number')->where('id', $certificate_id)->first()->number;

        $correct = CertificateAnswer::select('is_correct')
            ->where(['user_id' => $user_id, 'certificate_id' => $certificate_id, 'is_correct' => 1])
            ->get()->count();

        return $result = floor(($correct / $total) * 100);
    }

    /**
     * @OA\Post(
     * path="/api/UserCertificate/finish",
     * summary="new Certificate",
     * description="store new Certificate",
     * operationId="create",
     * tags={"UserCertificate"},
     *  security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="finish certifiate",
     *    @OA\JsonContent(
     *       required={"certificate_id"},
     *       @OA\Property(property="certificate_id", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *          @OA\Property(property="user", type="string", example="Certificate ended"),
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
    function finishCertificate(Request $request)
    {
        //validate on user id
        $user = auth('sanctum')->user();

        //validate on certificate id
        Validator::make($request->all(), ['certificate_id' => 'required|exists:certificates,id']);

        $this->storeAnswers($user->id, $request->certificate_id);
        $result =   $this->calculateResult($user->id, $request->certificate_id);

        UserCertificate::where(['user_id' => $user->id, 'certificate_id' => $request->certificate_id])->update(
            [
                'creditional' => Str::random(20),
                'completed' => 1,
                'result' => $result,
            ]
        );
        return $this->ApiResponse(200, 'your result is ', null, $result . '%');
    }
}
