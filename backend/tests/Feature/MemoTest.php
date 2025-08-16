<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Menu;
use App\Models\Memo;
use Illuminate\Support\Arr;

class MemoTest extends TestCase
{

    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * @param User $user
     * @return Menu
     */
    protected function createMenu(User $user): Menu
    {
        return Menu::factory()->create(['user_id' => $user->id]);
    }

    /** 
     * @return User
     */
    protected function createOtherUser(): User
    {
        return User::factory()->create();
    }

    /**
     * @param Menu $menu
     * @return array
     */
    protected function createNewMemoData(Menu $menu): array
    {
        return Memo::factory()->raw([
            'menu_id' => $menu->id,
        ]);
    }

    /**
     * @param User $user
     * @param Menu $menu
     * @return Memo
     */
    protected function createMemo(User $user, Menu $menu): Memo
    {
        return Memo::factory()->create([
            'user_id' => $user->id,
            'menu_id' => $menu->id,
        ]);
    }

    public function test_guest_cannot_store_memos()
    {
        $initial_memo_count = Memo::count();
        $menu = $this->createMenu($this->user);
        $new_memo_data = $this->createNewMemoData($menu);
        $response = $this->post(route('menus.memos.store', $menu), $new_memo_data);
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('memos', $new_memo_data);
        $this->assertDatabaseCount('memos', $initial_memo_count);
    }

    public function test_user_can_store_memos_to_own_menus()
    {
        $menu = $this->createMenu($this->user);
        $new_memo_data = $this->createNewMemoData($menu);
        $response = $this->actingAs($this->user)
                        ->post(route('menus.memos.store', $menu), $new_memo_data);
        $response->assertRedirectBack();
        $response->assertSessionHas('flash_message');
        $expected_data = array_merge($new_memo_data, ['user_id' => $this->user->id]);
        $this->assertDatabaseHas('memos', $expected_data);
    }

    public function test_user_can_store_memos_to_others_menus()
    {
        $other_user = $this->createOtherUser();
        $menu = $this->createMenu($other_user);
        $new_memo_data = $this->createNewMemoData($menu);
        $response = $this->actingAs($this->user)
                        ->post(route('menus.memos.store', $menu), $new_memo_data);
        $response->assertRedirectBack();
        $expected_data = array_merge($new_memo_data, ['user_id' => $this->user->id]);
        $this->assertDatabaseHas('memos', $expected_data);
    }

    public function test_guest_cannot_update_memos()
    {
        $menu = $this->createMenu($this->user);
        $memo = $this->createMemo($this->user, $menu);
        $new_memo_data = $this->createNewMemoData($menu);
        
        $response = $this->patch(route('menus.memos.update', [$menu, $memo]), $new_memo_data);
        $response->assertRedirect(route('login'));
        $expected_data = Arr::only($memo->toArray(), [
            'user_id',
            'menu_id',
            'content',
        ]);
        $this->assertDatabaseHas('memos', $expected_data);
        $this->assertDatabaseMissing('memos', $new_memo_data);
    }

    public function test_user_cannot_update_others_memos()
    {
        $other_user = $this->createOtherUser();
        $menu = $this->createMenu($this->user);
        $other_users_memo = $this->createMemo($other_user, $menu);
        $new_memo_data = $this->createNewMemoData($menu);
        $expected_data = Arr::only($other_users_memo->toArray(), [
            'user_id',
            'menu_id',
            'content',
        ]);

        $response = $this->actingAs($this->user)
                        ->patch(route('menus.memos.update', [$menu, $other_users_memo]), $new_memo_data);
        $response->assertForbidden();
        $this->assertDatabaseHas('memos', $expected_data);
        $this->assertDatabaseMissing('memos', $new_memo_data);
    }

    public function test_user_can_update_own_memos()
    {
        $menu = $this->createMenu($this->user);
        $memo = $this->createMemo($this->user, $menu);
        $new_memo_data = $this->createNewMemoData($menu);
        $old_data = Arr::only($memo->toArray(), [
            'user_id',
            'menu_id',
            'content'
        ]);
        
        $response = $this->actingAs($this->user)
                        ->patch(route('menus.memos.update', [$menu, $memo]), $new_memo_data);
        $response->assertRedirectBack();
        $response->assertSessionHas('flash_message');
        $this->assertDatabaseMissing('memos', $old_data);
        $this->assertDatabaseHas('memos', $new_memo_data);
    }

    public function test_user_cannot_update_memo_via_unrelated_menu_route()
    {
        $menu1 = $this->createMenu($this->user);
        $memo_for_menu1 = $this->createMemo($this->user, $menu1);

        $menu2 = $this->createMenu($this->user);
        $new_memo_data = $this->createNewMemoData($menu2);

        $response = $this->actingAs($this->user)
                        ->patch(route('menus.memos.update', [$menu2, $memo_for_menu1], $new_memo_data));
        $response->assertNotFound();
        $this->assertDatabaseMissing('memos', $new_memo_data);
    }

    public function test_guest_cannot_destroy_memos()
    {
        $menu = $this->createMenu($this->user);
        $memo = $this->createMemo($this->user, $menu);
        $expected_data = Arr::only($memo->toArray(), [
            'user_id',
            'menu_id',
            'content'
        ]);

        $response = $this->delete(route('menus.memos.destroy', [$menu, $memo]));
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('memos', $expected_data);
    }

    public function test_user_cannot_destroy_other_users_memos()
    {
        $other_user = $this->createOtherUser();
        $menu = $this->createMenu($this->user);
        $other_users_memo = $this->createMemo($other_user, $menu);
        $old_data = Arr::only($other_users_memo->toArray(), [
            'user_id',
            'menu_id',
            'content',
        ]);

        $response = $this->actingAs($this->user)
                        ->delete(route('menus.memos.destroy', [$menu, $other_users_memo]));
        $response->assertForbidden();
        $this->assertDatabaseHas('memos', $old_data);
    }

    public function test_user_can_destroy_own_memos()
    {
        $menu = $this->createMenu($this->user);
        $memo = $this->createMemo($this->user, $menu);

        $response = $this->actingAs($this->user)
                        ->delete(route('menus.memos.destroy', [$menu, $memo]));
        $response->assertRedirectBack();
        $response->assertSessionHas('flash_message');
        $this->assertSoftDeleted($memo);
    }

    public function test_user_cannot_store_duplicate_memo_for_the_same_menu()
    {
        $menu = $this->createMenu($this->user);
        $this->createMemo($this->user, $menu);
        $new_memo_data = $this->createNewMemoData($menu);
        $initial_memo_count = Memo::count();

        $response = $this->actingAs($this->user)
                        ->post(route('menus.memos.store', $menu), $new_memo_data);
        $response->assertRedirectBack();
        $response->assertInvalid('content');
        $this->assertDatabaseCount('memos', $initial_memo_count);
    }

    public function test_user_cannot_destroy_memo_via_unrelated_menu_route()
    {
        $menu1 = $this->createMenu($this->user);
        $memo_for_menu1 = $this->createMemo($this->user, $menu1);

        $menu2 = $this->createMenu($this->user);

        $expected_data = Arr::only($memo_for_menu1->toArray(), [
            'user_id',
            'menu_id',
            'content'
        ]);

        $response = $this->actingAs($this->user)
                        ->delete(route('menus.memos.destroy', [$menu2, $memo_for_menu1]));
        $response->assertNotFound();
        $this->assertNotSoftDeleted('memos', $expected_data);
    }

    
}
