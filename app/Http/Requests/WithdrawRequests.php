<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequests extends FormRequest
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
            'attachment' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'status' => 'required',
        ];
    }

     public function messages(): array
    {
        return [
            'name.required' => 'The Name is required',
            'name.string' => 'The Name must be a string',
            'name.max' => 'The Name must not exceed 255 characters',
            'attachment.required' => 'The attachment field is required',
            'attachment.image' => 'The attachment must be an image',
            'attachment.mimes' => 'The attachment must be a file of type: jpeg, png, jpg, svg',
            'attachment.max' => 'The attachment must not exceed 2048 kilobytes',
            'status.required' => 'Approval status is required',
        ];
    }
}
