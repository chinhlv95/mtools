<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProjectRiskEditRequest extends Request
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
            'risk_title' => 'required|min:5|max:255',
            'propability' => 'required|numeric',
            'mitigration_plan' =>'required|max:500',
        ];
    }
}
