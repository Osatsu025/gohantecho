<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Menu_show') }}
        </h2>
    </x-slot>
        
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="overflow-x-auto">
        <div class="p-6 sm:p-8 bg-base-100 shadow-md rounded-lg">
          <div class="max-w-xl mx-auto">
            
            @if (session('flash_message'))
            <div role="alert" class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('flash_message') }}</span>
            </div>
            @endif

            @if (session('error_message'))
              <div role="alert" class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error_message') }}</span>
              </div>
            @endif

            <div class="flex items-center mb-2">
              <h1 class="text-2xl mr-10">{{ $menu->title }}</h1>
              @can('update', $menu)
              <a role="button" class="btn mr-2" href="{{ route('menus.edit', $menu) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                  <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                </svg>
                {{ __('Edit') }}
              </a>
              @endcan
              @can('delete', $menu)
              <button class="btn btn-error mr-6" onclick="delete_modal.showModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                </svg>
                {{ __('Delete') }}
              </button>
              <dialog id="delete_modal" class="modal">
                <div class="modal-box">
                  <h3 class="text-lg font-bold">{{ __('Do you really want to delete this menu ?') }}</h3>
                  <div class="modal-action">
                    <form method="dialog">
                      <button class="btn">{{ __('Cancel') }}</button>
                    </form>
                    <form method="POST" action="{{ route('menus.destroy', $menu) }}">
                      @csrf
                      @method('delete')
                      <input type="submit" value="{{ __('Delete') }}" class="btn btn-error">
                    </form>
                  </div>
                </div>
              </dialog>
              @endcan
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
            </div>
            <div class="mb-8">
              @if ($menu->user)
              <a href="{{ query_route('menus.index', ['author' => $menu->user->name, 'page' => 1]) }}" class="text-lg">{{ $menu->user->name }}</a>
              @else
              <h2 class="text-1">{{ __('Unknown') }}</h2>
              @endif
            </div>
            <p class="mb-4">{!! nl2br(e($menu->content)) !!}</p>
            <div class="mb-4">
              <p>タグ</p>
                @foreach ($menu->tags as $tag)
                  @php
                    $is_active_tag = in_array($tag->id, $tag_ids);

                    if ($is_active_tag) {
                      $new_tag_ids = array_values(array_filter($tag_ids, fn($id) => $id != $tag->id));
                    } else {
                      $new_tag_ids = array_values(array_unique(array_merge($tag_ids, [$tag->id])));
                    }
                  @endphp
                  <a href="{{ query_route('menus.index', ['tag_ids' => $new_tag_ids, 'page' => 1]) }}" role="button" class="btn btn-xs @if($is_active_tag) btn-soft btn-primary @endif">{{ $tag->name }}</a>
                @endforeach
            </div>
            <div>
              <p>自分用メモ</p>
              @if($memo)
              <form method="POST" action="{{ route('menus.memos.update', [$menu, $memo]) }}">
                @csrf
                @method('PATCH')
                @error('content')
                  <p class="text-error-content">{{ $message }}</p>
                @enderror
                <textarea name="content" id="" class="textarea w-xl size-70" placeholder="アレンジポイントや感想など(他のユーザには公開されません)">{!! nl2br(e($memo->content)) !!}</textarea>
                <div class="flex gap-2">
                  <button type="submit" class="btn btn-sm flex-grow btn-neutral">保存</button>
                  <button type="button" class="btn btn-sm btn-error" onclick="memo_delete_modal.showModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                      <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                    </svg>
                  </button>
                </div>
                <dialog id="memo_delete_modal" class="modal">
                  <div class="modal-box">
                    <p>メモを削除しますか？</p>
                    <div class="modal-action">
                      <form method="dialog">
                        <button class="btn">キャンセル</button>
                      </form>
                      <form method="POST" action="{{ route('menus.memos.destroy', [$menu, $memo]) }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-error">
                          削除
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                          </svg>
                        </button>
                      </form>
                    </div>
                  </div>
                </dialog>
              </form>
              @else
              <form method="POST" action="{{ route('menus.memos.store', $menu) }}">
                @csrf
                @error('content')
                  <p class="text-error-content">{{ $message }}</p>
                @enderror
                <textarea name="content" id="" class="textarea w-xl size-70" placeholder="アレンジポイントや感想など(他のユーザには公開されません)"></textarea>
                <div class="flex">
                  <button type="submit" class="btn btn-sm btn-neutral flex-grow">保存</button>
                </div>
              </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</x-app-layout>