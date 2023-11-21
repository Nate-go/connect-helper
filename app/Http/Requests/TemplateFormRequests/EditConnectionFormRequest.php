<?php

namespace App\Http\Requests\ConnectionFormRequests;

use Illuminate\Foundation\Http\FormRequest;

class EditConnectionFormRequest extends FormRequest
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
            'tagIds' => 'required|array',
            'name' => 'required|string',
            'note' => 'required|string',
            'status' => 'required',
        ];
    }
}
