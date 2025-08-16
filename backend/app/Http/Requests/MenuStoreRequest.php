<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'public' => ['required', 'boolean'],
            'tag_ids'   => ['nullable', 'array'],
            'tag_ids.*' => ['numeric', Rule::exists('tags', 'id')],
            'input_tags' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'public' => $this->boolean('public'),
            'tag_ids' => $this->input('tag_ids', []),
        ]);
    } 
}
