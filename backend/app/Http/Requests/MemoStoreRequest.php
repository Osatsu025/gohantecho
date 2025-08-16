<?php

namespace App\Http\Requests;

use App\Models\Memo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MemoStoreRequest extends FormRequest
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
            'content' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->isMethod('post')) {
                $user = $this->user();
                $menu = $this->route('menu');

                if ($user && $menu) {
                    $isExists = Memo::where('user_id', $user->id)
                                    ->where('menu_id', $menu->id)
                                    ->exists();
                }

                if ($isExists) {
                    $validator->errors()->add('content', 'このメニューにはすでにメモが登録されています');
                }
            }
        });
    }
}
