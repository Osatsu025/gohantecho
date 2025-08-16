<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-base-content">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-base-content">
        {{ __("If you delete your account, you will no longer be able to log in or post, and you will not be able to restore it yourself. You can choose whether or not you want to delete your posted menus at the same time from the options.") }}
        </p>
    </header>

    <button class="btn btn-error" onclick="delete_modal.showModal()">{{ __('Delete') }}</button>
    <dialog id="delete_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">{{ __('If you delete your account, you will be unable to log in or post in the future. This action cannot be undone. Are you sure you want to delete your account?') }}</h3>
            <p class="py-4">{{ __('') }}</p>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <label class="label cursor-pointer my-4">
                    <input type="checkbox" checked="checked" class="checkbox" name="is_delete_menus" value="1">
                    {{ __('Delete all menu you have posted along with your account') }}
                </label>
                <div class="modal-action">
                    <button type="button" class="btn" onclick="delete_modal.close()">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-error">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </dialog>
</section>
