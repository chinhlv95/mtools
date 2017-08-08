<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AssignFormRequest extends Request
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
            'email' => 'required|email',
            'startDate' => 'required|date_format:d/m/Y|before:endDate',
            'endDate' => 'required|date_format:d/m/Y|after:startDate',
            'effort' => 'required|numeric|max:100',
            'memberName' => 'required'
        ];
    }
}
