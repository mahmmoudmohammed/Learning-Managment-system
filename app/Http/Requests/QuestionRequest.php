<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionRequest extends FormRequest
{
    private $minDifficulty = 1;
    private $maxDifficulty = 3;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'question' => ['required', Rule::unique('questions')->ignore($this->questionId)],
            'topic_id' => 'required|integer|exists:topics,id',
            'difficulty' => 'required|integer|between:' . $this->minDifficulty . ',' . $this->maxDifficulty
        ];
    }
}
