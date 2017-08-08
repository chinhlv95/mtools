<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EditUserRequest extends Request
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
            'password' => 'min:6|regex:/^[a-zA-Z0-9@]+$/',
            'password_confirmation' => 'min:6|same:password|regex:/^[a-zA-Z0-9@]+$/',
            'first_name' => 'required'
        ];
    }

    public function messages() {
        return [
            'password.min' => 'Short passwords are easy to guess. Try one with at least 6 characters.',
            'password.regex' => 'Password should include some characters: a-z,A-Z,0-9,@',
            'password_confirmation.min' => 'Short confirmed password are easy to guess. Try one with at least 6 characters.',
            'password_confirmation.same' => 'The confirmed password don\'t match. Try again?',
            'password_confirmation.regex' => 'The confirmed password should include some characters: a-z,A-Z,0-9,@',
            'first_name.required' => 'You can\'t leave the First name empty.'
        ];
    }
}
