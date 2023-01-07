<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AddRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:255', //unique:task
            '_token' => 'required',
            'project' => 'integer|required'
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'   => false,
            'message'   => 'Validation errors',
            'error'      => $validator->errors()
        ]));
    }

}
