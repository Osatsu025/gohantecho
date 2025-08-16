<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Menu') }}
        </h2>
    </x-slot>
        
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="overflow-x-auto">
        <div class="p-6 sm:p-8 bg-base-100 shadow-md rounded-lg">
          <div class="mx-auto">

            @if (session('flash_message'))
            <div role="alert" class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('flash_message') }}</span>
            </div>
            @endif

            @if (session('error_message'))
            <div role="alert" class="alert alert-error mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error_message') }}</span>
            </div>
            @endif

            <form action="{{ route('menus.index') }}" id="search_form" class="mb-4">
              <div class="flex mb-2">
                @if ($author)
                <input type="hidden" name="author" value="{{ $author }}">
                @endif
                <label class="input">
                  <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <g
                      stroke-linejoin="round"
                      stroke-linecap="round"
                      stroke-width="2.5"
                      fill="none"
                      stroke="currentColor"
                    >
                      <circle cx="11" cy="11" r="8"></circle>
                      <path d="m21 21-4.3-4.3"></path>
                    </g>
                  </svg>
                  <input type="text" class="grow" placeholder="{{ __('search')}}" name="keyword" value="{{ $keyword }}" />
                </label>
                <a href="{{ route('menus.create') }}" class="btn btn-neutral ml-auto">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                  </svg>
                  新規投稿
                </a>
              </div>
              <button type="button" class="btn btn-sm @if($tag_ids || $is_only_favorited) btn-primary @endif" onclick="filter_modal.showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1.5A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 2.808V2h-11z"/>
                </svg>
                <span class="hidden sm:inline ml-1">絞り込み</span>
              </button>
              <select class="select select-sm w-auto" name="sort_type" id="sort_type" onchange="document.getElementById('search_form').submit();">
                @foreach ($sort_list as $name => $details)
                  <option value="{{ $name }}" @selected($sort_type === $name)>{{ $name }}</option>
                @endforeach
              </select>
              <dialog id="filter_modal" class="modal">
                <div class="modal-box">
                  <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                  </form>
                  <div class="flex justify-around mb-4">
                    <h3 class="text-lg font-bold">絞り込み検索</h3>
                    <button type="button" id="reset_filter_button" class="btn">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                        <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
                        <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
                      </svg>  
                      リセット
                    </button>
                  </div>
                  <div class="mb-4">
                    <h4 class="text-base font-bold mb-2">タグ</h4>
                    @foreach($tags as $tag)
                      <input
                        type="checkbox"
                        aria-label="{{ $tag->name }}"
                        class="btn btn-xs mr-2 mb-2 tag-checkbox-selector"
                        data-tag-name="{{ $tag->name }}"
                        value="{{ $tag->id }}"
                        id="tag_{{ $tag->id }}"
                        name="tag_ids[]"
                        @checked( is_array($tag_ids) && in_array($tag->id, $tag_ids)) )
                      />
                    @endforeach
                  </div>
                  <div class="mb-4">
                    <label>
                      <input type="checkbox" class="checkbox" name="is_only_favorited" id="is_only_favorited" value="1" @checked($is_only_favorited) />
                      お気に入りのメニューのみを表示
                    </label>
                  </div>
                  <div class="modal-action">
                    <button class="btn btn-primary" form="search_form" type="submit">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                      </svg>
                      検索
                    </button>
                  </div>
                </div>
              </dialog>
            </form>

            @if (!empty($author) || !empty($tag_ids || !empty($is_only_favorited)))
            <div class="flex items-center gap-2 flex-wrap mb-4">
              <h3 class="text-lg font-semibold text-base-content/70">
                絞り込み条件：
              </h3>
              @endif
              @if (!empty($author))
                <a role="button" href="{{ query_route('menus.index', ['page' => 1], 'author') }}" class="btn btn-sm btn-soft btn-primary">
                  {{ $author }}
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                  </svg>
                </a>
              @endif
              @if (!empty($tag_ids))
                @foreach ($selected_tags as $selected_tag)
                  @php
                    $new_tag_ids = array_values(array_filter($tag_ids, fn($id) => $id != $selected_tag->id));
                  @endphp
                  <a role="button" href="{{ query_route('menus.index', ['tag_ids' => $new_tag_ids, 'page' => 1]) }}" class="btn btn-sm btn-soft btn-primary">
                    {{ $selected_tag->name }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                  </a>  
                @endforeach
              @endif
              @if ($is_only_favorited)
              <a role="button" href="{{ query_route('menus.index', ['page' => 1], 'is_only_favorited') }}" class="btn btn-sm btn-soft btn-primary">
                お気に入り
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                </svg>
              </a>
              @endif
            </div>

            <div class="tabs tabs-lift">
              <input type="radio" name="author_tabs" class="tab flex-auto" aria-label="あなたのメニュー" checked="checked">
              <div class="tab-content bg-base-100 border-base-300 p-6">
                <table class="table mb-4">
                  <thead>
                    <tr>
                      <th>タイトル</th>
                      <th>タグ</th>
                      <th>お気に入り</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($users_menus as $menu)
                    <tr class="hover:bg-base-300">
                      <td><a href="{{ query_route('menus.show', ['menu' => $menu]) }}">{{ $menu->title }}</a></td>
                      <td>
                        @foreach ($menu->tags as $tag)
                          @php
                            $is_active_tag = in_array($tag->id, $tag_ids);

                            if ($is_active_tag) {
                              $new_tag_ids = array_values(array_filter($tag_ids, fn($id) => $id != $tag->id));
                            } else {
                              $new_tag_ids = array_values(array_unique(array_merge($tag_ids, [$tag->id])));
                            }
                          @endphp
                          <a href="{{ query_route('menus.index', ['tag_ids' => $new_tag_ids, 'page' => 1]) }}"
                            role="button" class="btn btn-xs @if($is_active_tag) btn-soft btn-primary @endif">{{ $tag->name }}</a>
                        @endforeach
                      </td>
                      <td class="text-center">
                        <form method="POST" action="{{ query_route('menus.favorite', ['menu' => $menu]) }}" class="inline-flex items-center space-x-1">
                          @csrf
                          <button type="submit">
                            @if($menu->favoritedUsers->contains(Auth::user()))
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-heart-fill fill-pink-500" viewBox="0 0 16 16">
                              <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                              <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                            @endif
                          </button>
                          <span class="@if ($menu->favoritedUsers->contains(Auth::user())) text-pink-500 @endif">{{ $menu->favorited_users_count }}</span>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="mb-4">
                  {{ $users_menus->appends(request()->except('users_page'))->links() }}
                </div>
              </div>
              
              <input type="radio" name="author_tabs" class="tab flex-auto" aria-label="みんなのメニュー">
              <div class="tab-content bg-base-100 border-base-300 p-6">
                <table class="table mb-4">
                  <thead>
                    <tr>
                      <th>タイトル</th>
                      <th>作者</th>
                      <th>タグ</th>
                      <th>お気に入り</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($others_menus as $menu)
                    <tr class="hover:bg-base-300">
                      <td><a href="{{ query_route('menus.show', ['menu' => $menu]) }}">{{ $menu->title }}</a></td>
                      @if ($menu->user)
                      <td><a href="{{ query_route('menus.index', ['author' => $menu->user->name, 'page' => 1]) }}">{{ $menu->user->name }}</a></td>
                      @else
                      <td>{{ __('Unknown') }}</td>
                      @endif
                      <td>
                        @foreach ($menu->tags as $tag)
                          @php
                            $is_active_tag = in_array($tag->id, $tag_ids);

                            if ($is_active_tag) {
                              $new_tag_ids = array_values(array_filter($tag_ids, fn($id) => $id != $tag->id));
                            } else {
                              $new_tag_ids = array_values(array_unique(array_merge($tag_ids, [$tag->id])));
                            }
                          @endphp
                          <a href="{{ query_route('menus.index', ['tag_ids' => $new_tag_ids, 'page' => 1]) }}"
                            role="button" class="btn btn-xs @if($is_active_tag) btn-soft btn-primary @endif">{{ $tag->name }}</a>
                        @endforeach
                      </td>
                      <td class="text-center">
                        <form method="POST" action="{{ query_route('menus.favorite', ['menu' => $menu]) }}" class="inline-flex items-center space-x-1">
                          @csrf
                          <button type="submit">
                            @if($menu->favoritedUsers->contains(Auth::user()))
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-heart-fill fill-pink-500" viewBox="0 0 16 16">
                              <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                              <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                            @endif
                          </button>
                          <span class="@if ($menu->favoritedUsers->contains(Auth::user())) text-pink-500 @endif">{{ $menu->favoritedUsers()->count() }}</span>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="mb-4">
                  {{ $others_menus->appends(request()->except('others_page'))->links() }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<script>
  // DOMが完全に読み込まれた後にスクリプト実行
  document.addEventListener('DOMContentLoaded', function() {
    const resetButton = document.getElementById('reset_filter_button');
    const checkboxes = document.querySelectorAll('#filter_modal .tag-checkbox-selector');
    const favorited_checkbox = document.getElementById('is_only_favorited');

    function updateResetButtonState() {
      const isAnyTagsChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
      const isFavoritedChecked = favorited_checkbox.checked;
        if (isAnyTagsChecked || isFavoritedChecked) {
          resetButton.classList.add('btn-neutral');
        } else {
          resetButton.classList.remove('btn-neutral');
        }
    }

    resetButton.addEventListener('click', function() {
      checkboxes.forEach(checkbox => checkbox.checked = false);
      favorited_checkbox.checked = false;
      updateResetButtonState();
    });

    checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateResetButtonState));
    favorited_checkbox.addEventListener('change', updateResetButtonState);

    updateResetButtonState();

  });
</script>