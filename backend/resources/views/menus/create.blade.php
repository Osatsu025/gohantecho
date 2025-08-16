<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Menu_create') }}
        </h2>
    </x-slot>
        
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="overflow-x-auto">
        <div class="p-6 sm:p-8 bg-base-100 shadow-md rounded-lg">
          <div class="max-w-xl mx-auto">
            <form method="POST" action="{{ route('menus.store') }}">
              @csrf
              <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-l border p-4">
                <legend class="fieldset-legend">新メニュー</legend>

                @error('title')
                  <p class="text-error-content">{{ $message }}</p>
                @enderror
                <label for="title" class="label">{{ __('Title') }}</label>
                <input type="text" class="input w-lg" placeholder="タイトル" name="title" value="{{ old('title') }}" required>

                @error('content')
                  <p class="text-error-content">{{ $message }}</p>
                @enderror
                <label for="content" class="label">{{ __('Content') }}</label>
                <textarea name="content" id="content" class="textarea w-lg size-100" placeholder="{{ __('Write Ingredients and order of your original recipe or Other sites URL or Restaurants menu you ate') }}" required>{{ old('content') }}</textarea>

                @error('input_tags')
                  <p class="text-error-content">{{ $message }}</p>
                @enderror
                <label for="tag_input">タグ</label>
                <input type="text" class="input input-xs" id="tag_input" name="input_tags" placeholder="下から選択するか、半角スペースで区切って入力">
                <div id="tag_div">
                  @foreach ($tags as $tag)
                    <input
                      type="checkbox"
                      aria-label="{{ $tag->name }}"
                      class="btn btn-xs mr-2 mb-2 tag-checkbox-selector"
                      data-tag-name="{{ $tag->name }}"
                      value="{{ $tag->id }}"
                      id="tag_{{ $tag->id }}"
                      name="tag_ids[]"
                      onchange="add_tag('{{ json_encode($tag, JSON_HEX_APOS | JSON_HEX_QUOT) }}', this)"
                      @checked( is_array(old('tag_ids')) && in_array($tag->id, old("tag_ids")) )
                    />
                  @endforeach
                </div>

                <label for="public">
                  {{ __('Public') }}
                  <input type="hidden" name="public" value="0">
                  <input
                    type="checkbox"
                    class="toggle"
                    name="public"
                    id="public"
                    value="1"
                    {{ old('public', '1') == '1' ? 'checked' : '' }}
                  />
                </label>

                <button type="submit" class="btn btn-neutral">{{ __('Submit') }}</button>

              </fieldset>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>