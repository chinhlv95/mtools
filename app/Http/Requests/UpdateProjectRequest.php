<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateProjectRequest extends Request
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
            'department_id' => 'regex: /^[0-9+]*$/',
            'division' => 'regex: /^[0-9+]*$/',
            'name' => 'required|unique:projects,name,'.$this->id,
            'department' => 'regex: /^[0-9+]*$/',
            'status' => 'required',
            'type_id' => 'required',
            'brse' => 'required',
            'plant_start_date' => 'required|date_format:d/m/Y',
            'plant_end_date' => 'required|date_format:d/m/Y',
            'actual_start_date' => 'date_format:d/m/Y',
            'actual_end_date' => 'date_format:d/m/Y',
            'plant_total_effort' => 'numeric',
            'actual_effort' => 'numeric',
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
