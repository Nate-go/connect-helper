<?php

namespace App\Http\Requests\TemplateFormRequests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTemplateFormRequest extends FormRequest
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
            'subject' => 'required|string',
            'content' => 'required|string',
            'type' => 'required',
            'template_group_id' => 'required',
            'status' => 'required',
        ];
    }
}
