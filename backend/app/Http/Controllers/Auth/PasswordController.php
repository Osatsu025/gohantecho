<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['status' => 'password-updated']);
        } catch (ValidationException $e) {
            // バリデーションエラーの場合は、エラー内容をJSONで返す
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            // その他の予期せぬエラー
            return response()->json(['message' => 'パスワードの更新に失敗しました。'], 500);
        }
    }
}
