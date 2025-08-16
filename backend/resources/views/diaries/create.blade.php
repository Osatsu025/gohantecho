<x-app-layout>

  <x-slot name="header">
      <h2 class="font-semibold text-xl text-base-content leading-tight">
          {{ __('Diary') }}
      </h2>
  </x-slot>
        
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="overflow-x-auto">
        <div class="p-6 sm:p-8 bg-base-100 shadow-md rounded-lg">
          <div class="max-w-xl mx-auto">

            <form method="POST" action="{{ route('diaries.store') }}">
              @csrf

              <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4">
                <legend class="fieldset-legend">日記作成</legend>

                  <label for="date" class="label">日付</label>
                  <button popovertarget="cally-popover1" class="input input-border" id="cally1" style="anchor-name:--cally1" name="date">
                    {{ old('date') ?: '日付を選択' }}
                  </button>
                  <div popover id="cally-popover1" class="dropdown bg-base-100 rounded-box shadow-lg" style="position-anchor:--cally1">
                    <calendar-date class="cally" onchange={document.getElementById('cally1').innerText = this.value}>
                      <svg aria-label="Previous" class="fill-current size-4" slot="previous" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.75 19.5 8.25 12l7.5-7.5"></path></svg>
                      <svg aria-label="Next" class="fill-current size-4" slot="next" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="m8.25 4.5 7.5 7.5-7.5 7.5"></path></svg>
                      <calendar-month></calendar-month>
                    </calendar-date>
                  </div>

                  <label for="title" class="label">タイトル</label>
                  <input type="text" class="input">

                  <div id="menus_area"></div>

                  <button class="btn" onclick="menu_search_modal.showModal()">＋メニューを追加</button>
                  <dialog id="menu_search_modal" class="modal">
                    <div class="modal-box">

                      <form action="{{ route('diaries.create') }}" id="search_form" class="mb-4">
                        <div class="flex mb-2">
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
                        </div>
                      </form>

                      <div class="tabs tabs-lift">
                        <input type="radio" name="author_tabs" class="tab flex-auto" aria-label="あなたのメニュー" checked="checked">
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                          <table class="table mb-4">
                            <thead>
                              <tr>
                                <th>タイトル</th>
                                <th>タグ</th>
                                <th>お気に入り</th>
                                <th>追加</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($users_menus as $menu)
                              <tr class="hover:bg-base-300">
                                <td><a href="{{ query_route('diaries.create', ['menu' => $menu]) }}">{{ $menu->title }}</a></td>
                                <td>
                                  @foreach ($menu->tags as $tag)
                                    <button type="button" class="btn btn-xs">{{ $tag->name }}</a>
                                  @endforeach
                                </td>
                                <td class="text-center">
                                  <button type="button">
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
                                </td>
                                <td>
                                  <button type="button" class="btn" onclick="selectMenu({{ $menu->id }}, '{{ $menu->title }}')">追加</button>
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
                                <th>追加</th>
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
                                    <button type="button" class="btn btn-xs">{{ $tag->name }}</button>
                                  @endforeach
                                </td>
                                <td class="text-center">
                                  <button type="button">
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

                      <div class="modal-action">
                        <button type="button" class="btn" onclick="menu_search_modal.close()"></button>
                      </div>
                    </div>
                  </dialog>

              </fieldset>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="module" src="https://cdn.jsdelivr.net/gh/vanillawc/cally@1/dist/cally.js"></script>
  <script>
    function selectMenu(menu_id, menu_title) {
      const menus_area = document.getElementById('menus_area');
      const menu = document.createElement('p');
      menu.setAttribute('type', 'text');
      menu.setAttribute('value', menu_title);
      menus_area.appendChild(menu);
      document.getElementById('menu_search_modal').close();
    }
  </script>
</x-app-layout>
