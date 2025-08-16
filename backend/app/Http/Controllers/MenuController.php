<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuIndexRequest;
use App\Http\Requests\MenuStoreRequest;
use App\Models\Memo;
use App\Models\Menu;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\View\View;
use Illuminate\Support\Str;

class MenuController extends Controller
{

    public function index(MenuIndexRequest $request): View {

        $this->authorize('viewAny', Menu::class);

        $user_id = Auth::id();
        $validated = $request->validated();

        $keyword = $validated['keyword'] ?? null;
        $author = $validated['author'] ?? null;
        $sort_list = Menu::SORT_LIST;
        $sort_type = $validated['sort_type'] ?? array_key_first(Menu::SORT_LIST);
        $tag_ids = $validated['tag_ids'] ?? [];
        $selected_tags = collect();
        if (!empty($tag_ids)) {
            $selected_tags = Tag::whereIn('id', $tag_ids)->get();
        }
        $is_only_favorited = $validated['is_only_favorited'] ?? null;

        $query = Menu::query()
            ->with(['user', 'tags', 'favoritedUsers'])
            ->filterByPublic($user_id)
            ->searchByKeyword($keyword)
            ->filterByAuthor($author)
            ->filterByTagIds($tag_ids)
            ->filterByFavorited($is_only_favorited, $user_id)
            ->sortByConditions($sort_type, $user_id);

        $query->withCount('favoritedUsers');

        $other_query = clone $query;

        $users_menus = $query->where('menus.user_id', $user_id)->paginate(10, ['*'], 'users_page');
        $others_menus = $other_query->whereNot('menus.user_id', $user_id)->paginate(10, ['*'], 'others_page');

        $tags = Tag::all();

        return view('menus.index', compact(
            'users_menus',
            'others_menus',
            'keyword',
            'author',
            'tag_ids',
            'selected_tags',
            'is_only_favorited',
            'sort_list',
            'sort_type',
            'tags',
        ));
    }

    public function create(): View {
        $this->authorize('create', Menu::class);

        $tags = Tag::all();
        return view('menus.create', compact('tags'));
    }

    public function store(MenuStoreRequest $request): RedirectResponse {
        $this->authorize('create', Menu::class);

        $validated = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        try {
            $menu = FacadesDB::transaction(function () use ($user, $validated) {
                $menu = $user->menus()->create($validated);

                $tag_names_str = $validated['input_tags'] ?? '';
                $tag_ids = Tag::findOrCreateByName($tag_names_str);

                $menu->tags()->attach($tag_ids);

                return $menu;
            });
        } catch (\Throwable $e) {
            FacadesLog::error('メニューの登録に失敗しました', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error_message', 'メニューの登録に失敗しました');
        }

        $message = $menu->title . 'を登録しました';

        return to_route('menus.index')->with('flash_message', $message);
    }

    public function show(Menu $menu): View {
        $this->authorize('view', $menu);

        $user = Auth::user();

        $menu->load(['user', 'tags', 'memos'])
            ->loadCount('favoritedUsers');
        $memo = $menu->memos->where('user_id', $user->id)->first();
        $tag_ids = request()->query('tag_ids', []);

        return view('menus.show', compact('menu', 'tag_ids', 'memo'));
    }

    public function edit(Menu $menu): View {

        $this->authorize('update', $menu);

        $menu->load(['user', 'tags']);
        $tags = Tag::all();

        $input_selected_tags = $menu->formatted_tags;

        return view('menus.edit', compact(
            'menu',
            'tags',
            'input_selected_tags',
        ));
    }

    public function update(MenuStoreRequest $request, Menu $menu): RedirectResponse {
        $this->authorize('update', $menu);

        $validated = $request->validated();

        try {
            FacadesDB::transaction(function () use ($menu, $validated) {
                $menu->update($validated);

                $tag_names_str = $validated['input_tags'] ?? '';
                $tag_ids = Tag::findOrCreateByName($tag_names_str);

                $menu->tags()->sync($tag_ids);
            });
        } catch (\Throwable $e) {
            FacadesLog::error('メニューの更新に失敗しました', [
                'user_id' => $menu->user_id,
                'menu_id' => $menu->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error_message', 'メニューの更新に失敗しました');
        }

        $message = $menu->title . 'を更新しました';

        return to_route('menus.show', $menu)->with('flash_message', $message);
    }

    public function destroy(Menu $menu): RedirectResponse {

        $this->authorize('delete', $menu);

        try {
            $menu->delete();
        } catch(\Throwable $e) {
            FacadesLog::error('メニューの削除に失敗しました', [
                'menu_id' => $menu->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error_message', 'メニューの削除に失敗しました');
        }
        
        $message = $menu->title . 'を削除しました';

        return to_route('menus.index')->with('flash_message', $message);
    }

    public function favorite(Menu $menu): RedirectResponse {

        $this->authorize('view', $menu);
        
        /** @var User $user */
        $user = Auth::user();

        try {
            $user->favoriteMenus()->toggle($menu);
        } catch (\Throwable $e) {
            FacadesLog::error('お気に入り操作に失敗しました', [
                'menu_id' => $menu->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error_message', 'お気に入り操作に失敗しました');
        }

        return back();
    }

}