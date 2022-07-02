<?php

namespace App\Http\Requests\Word;

use Illuminate\Foundation\Http\FormRequest;

class StoreWordRequest extends FormRequest
{
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
            'user_id'     => 'required|numeric',
            'text'        => 'string|max:255',
            'video_title' => 'string|max:255',
            'url'         => 'string|max:255',
        ];
    }
}
