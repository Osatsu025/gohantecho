<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemoStoreRequest;
use App\Models\Memo;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;

class MemoController extends Controller
{

    public function store(MemoStoreRequest $request, Menu $menu): RedirectResponse
    {
        $this->authorize('create', Memo::class);

        $validated = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        try {
            $user->memos()->create([
                ...$validated,
                'menu_id' => $menu->id
            ]);
        } catch(\Throwable $e) {
            FacadesLog::error('メモの登録に失敗しました', [
                'menu_id' => $menu->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error_message', 'メモの登録に失敗しました');
        }

        return back()->with('flash_message', 'メモを登録しました');
    }

    public function update(MemoStoreRequest $request, Menu $menu, Memo $memo): RedirectResponse
    {
        $this->authorize('update', $memo);

        $validated = $request->validated();

        try {
            $memo->update($validated);
        } catch(\Throwable $e) {
            FacadesLog::error('メモの更新に失敗しました', [
                'memo_id' => $memo->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error_message', 'メモの更新に失敗しました');
        }

        return back()->with('flash_message', 'メモを更新しました');
    }

    public function destroy(Menu $menu, Memo $memo): RedirectResponse
    {
        $this->authorize('delete', $memo);
        
        try {
            $memo->delete();
        } catch(\Throwable $e) {
            FacadesLog::error('メモの削除に失敗しました', [
                'memo_id' => $memo->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error_message', 'メモの削除に失敗しました');
        }

        return back()->with('flash_message', 'メモを削除しました');
    }
}
