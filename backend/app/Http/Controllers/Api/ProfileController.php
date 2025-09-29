<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserDeleteRequest;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        try {
            $user->save();
        } catch (\Throwable $e) {
            FacadesLog::error('プロフィールの更新に失敗しました', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error_message', 'プロフィールの更新に失敗しました。時間をおいて再度お試しください']);
        
        }

        return response()->json(['status' => 'profile-updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDeleteRequest $request): JsonResponse
    {
        $user = $request->user();
        $should_delete_menus = $request->boolean('is_delete_menus');

        try {
            FacadesDB::transaction(function () use ($user, $should_delete_menus) {
                if ($should_delete_menus) {
                    $user->menus()->delete();
                }
                $user->delete();
            });
        } catch (\Throwable $e) {
            FacadesLog::error('アカウントの削除に失敗しました', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error_message', 'アカウントの削除に失敗しました。時間をおいて再度お試しください']);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'user-deleted']);
    }
}
