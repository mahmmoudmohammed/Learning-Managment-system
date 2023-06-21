<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetQuestionResource extends JsonResource
{
    private $useranswer;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource=$this->resource;
        return [
            'id'=>$resource->id,
            'question'=>$resource->question,
            'userAnswer'=> $resource->answer_id,
        'answers'=>GetAnswerResource::collection($resource->answers)
            ];
    }
}
