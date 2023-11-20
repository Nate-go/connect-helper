<?php

namespace App\Http\Requests\TagFormRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTagFormRequest extends FormRequest
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
        $userId = auth()->user()->id; // Get the current user's ID

        return [
            'name' => [
                'required',
                'string',
                'between:2,100',
                Rule::unique('tags')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                }),
            ],
        ];
    }
}
