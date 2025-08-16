<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuIndexRequest;
use App\Models\Menu;
use App\Models\Tag;
use App\Models\Time;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DiaryController extends Controller
{
    public function index()
    {

    }

    public function create(MenuIndexRequest $request): View
    {
        $this->authorize('viewAny', Menu::class);


        $user_id = Auth::id();
        $validated = $request->validated();

        $keyword = $validated['keyword'] ?? null;
        $selected_tags = collect();

        $query = Menu::query()
            ->with(['user', 'tags', 'favoritedUsers'])
            ->filterByPublic($user_id)
            ->searchByKeyword($keyword);

        $query->withCount('favoritedUsers');

        $other_query = clone $query;

        $users_menus = $query->where('menus.user_id', $user_id)->paginate(10, ['*'], 'users_page');
        $others_menus = $other_query->whereNot('menus.user_id', $user_id)->paginate(10, ['*'], 'others_page');

        $tags = Tag::all();

        $times = Time::all()->pluck('name');

        return view('diaries.create', compact(
            'users_menus',
            'others_menus',
            'keyword',
            'times',
        ));
    }

    public function store() 
    {

    }
}
