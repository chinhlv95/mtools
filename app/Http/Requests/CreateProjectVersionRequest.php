<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateProjectVersionRequest extends Request
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
            'name' => 'required',
            'status' => 'required',
            'start_date' => 'required|date_format:"d/m/Y"|before:end_date',
            'end_date' => 'required|date_format:"d/m/Y"',
        ];
    }
    public function messages()
    {
        return [
                'name.required' => 'The name field is required.',
        ];
    }
}
