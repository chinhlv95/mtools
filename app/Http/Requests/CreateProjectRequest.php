<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateProjectRequest extends Request
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
            'department' => 'regex: /^[0-9+]*$/',
            'division' => 'regex: /^[0-9+]*$/',
            'department_id' => 'regex: /^[0-9+]*$/',
            'name' => 'required|unique:projects',
            'status' => 'required',
            'type_id' => 'required',
            'brse' => 'required',
            'plant_start_date' => 'required|date_format:d/m/Y',
            'plant_end_date' => 'required|date_format:d/m/Y',
        ];
    }
    public function messages()
    {
        return [
            'department.regex' => 'The department field is required.',
            'division.regex' => 'The division field is required.',
            'department_id.regex' => 'The team field is required.',
        ];
    }
}
