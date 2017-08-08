<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateNewKptRequest extends Request
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
            "version"     => "required",
            "category"    => "required",
            "type"        => "required",
            "status"      => "required",
            "description" => "required",
            "action"      => "required"
        ];
    }
}
