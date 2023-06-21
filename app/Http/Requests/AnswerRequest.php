<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnswerRequest extends FormRequest
{
    private $inCorrect = 0;
    private $correct = 1;

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
            'answer' => ['required', Rule::unique('answers')->ignore($this->answerId)],
            'question_id' => 'required|exists:questions,id',
            'is_correct' => ['required', 'between:' . $this->inCorrect . ',' . $this->correct,
                Rule::unique('answers')->where(function ($query) {
                    return $query->where([
                        ['is_correct', '=', $this->correct],
                        ['question_id', '=', $this->question_id]
                    ]);
                })->ignore($this->answerId)
            ]
        ];
    }

}
