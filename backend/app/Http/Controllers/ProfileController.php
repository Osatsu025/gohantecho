<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UserDeleteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
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

            return back()->withInput()->with('error_message', 'プロフィールの更新に失敗しました。時間をおいて再度お試しください');
        
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(UserDeleteRequest $request): RedirectResponse
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

            return to_route('profile.edit')->with('error_message', 'アカウントの削除に失敗しました。時間をおいて再度お試しください');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
