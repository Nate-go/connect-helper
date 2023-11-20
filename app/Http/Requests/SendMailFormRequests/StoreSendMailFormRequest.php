<?php

namespace App\Http\Requests\SendMailFormRequests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSendMailFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'type' => 'required',
            'contactIds' => 'required|array'
        ];
    }
}
