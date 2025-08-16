<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        try {
            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }
        } catch (\Throwable $e) {
            FacadesLog::error('メールアドレスの有効化処理に失敗しました', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('verification.notice')->with('error_message', 'メールアドレスの有効化に失敗しました。');
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
