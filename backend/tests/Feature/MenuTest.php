<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected array $new_menu_data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->new_menu_data = array_merge(
            Menu::factory()->raw(),
            ['input_tags' => '']
        );
    }

    /**
     * @param User $user
     * @return Menu
     */
    protected function createMenu(User $user, ?array $elements = null): Menu
    {
        return Menu::factory()->create(array_merge(
            ['user_id' => $user->id],
            $elements ?? []
        ));
    }

    /** 
     * @return User
     */
    protected function createOtherUser(): User
    {
        return User::factory()->create();
    }


    public function test_guest_cannot_access_menus_index()
    {
        $response = $this->get(route('menus.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_menus_index()
    {
        $response = $this->actingAs($this->user)
                        ->get(route('menus.index'));
        $response->assertOk();
    }

    public function test_guest_cannot_access_menus_create()
    {
        $response = $this->get(route('menus.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_menus_create()
    {
        $response = $this->actingAs($this->user)
                        ->get(route('menus.create'));
        $response->assertOk();
    }

    public function test_guest_cannot_access_menus_show()
    {
        $menu = $this->createMenu($this->user);
        $response = $this->get(route('menus.show', $menu));
        $response->assertRedirect(route('login'));
    }
    
    public function test_user_can_access_menus_show()
    {
        $menu = $this->createMenu($this->user);
        $response = $this->actingAs($this->user)
                        ->get(route('menus.show', $menu));
        $response->assertOk();
    }

    public function test_user_can_access_other_users_public_menus_show()
    {
        $other_user = $this->createOtherUser();
        $other_users_menu = $this->createMenu($other_user, [
            'public' => true,
        ]);
        $response = $this->actingAs($this->user)
                        ->get(route('menus.show', $other_users_menu));
        $response->assertOk();
    }

    public function test_user_cannot_access_other_users_private_menus_show()
    {
        $other_user = $this->createOtherUser();
        $other_users_menu = $this->createMenu($other_user, [
            'public' => false,
        ]);
        $response = $this->actingAs($this->user)
                        ->get(route('menus.show', $other_users_menu));
        $response->assertForbidden();
    }

    public function test_guest_cannot_store_menus()
    {
        $initial_menu_count = Menu::count();
        $response = $this->post(route('menus.store'), $this->new_menu_data);
        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('menus', $initial_menu_count);
        $this->assertDatabaseMissing('menus', Arr::only($this->new_menu_data, ['title', 'content']));
    }

    public function test_user_can_store_menus()
    {
        $response = $this->actingAs($this->user)
                        ->post(route('menus.store'), $this->new_menu_data);
        $response->assertRedirect(route('menus.index'));
        $response->assertSessionHas('flash_message');

        $expected_data = Arr::only($this->new_menu_data, ['title', 'content', 'public']);
        $expected_data['user_id'] = $this->user->id;
        $this->assertDatabaseHas('menus', $expected_data);
    }

    public function test_guest_cannot_access_menus_edit()
    {
        $menu = $this->createMenu($this->user);
        $response = $this->get(route('menus.edit', $menu));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_menus_edit()
    {
        $menu = $this->createMenu($this->user);
        $response = $this->actingAs($this->user)
                        ->get(route('menus.edit', $menu));
        $response->assertOk();
    }

    public function test_user_cannot_access_other_users_menus_edit()
    {
        $other_user = $this->createOtherUser();
        $other_users_menu = $this->createMenu($other_user);
        $response = $this->actingAs($this->user)
                        ->get(route('menus.edit', $other_users_menu));
        $response->assertForbidden();
    }

    public function test_guest_cannot_update_menus()
    {
        $menu = $this->createMenu($this->user);
        $original_menu_data = $menu->only(['title', 'content', 'public']);
        $response = $this->patch(route('menus.update', $menu), $this->new_menu_data);
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('menus', $this->new_menu_data);
        $this->assertDatabaseHas('menus', $original_menu_data);
    }

    public function test_user_can_update_menus()
    {
        $menu = $this->createMenu($this->user);
        $original_menu_data = $menu->only(['title', 'content', 'public']);
        $response = $this->actingAs($this->user)
                        ->patch(route('menus.update', $menu), $this->new_menu_data);
        $response->assertRedirect(route('menus.show', $menu));
        $response->assertSessionHas('flash_message');

        $expected_data = Arr::only($this->new_menu_data, ['title', 'content', 'public']);
        $expected_data['id'] = $menu->id;
        $expected_data['user_id'] = $this->user->id;
        $this->assertDatabaseHas('menus', $expected_data);
        $this->assertDatabaseMissing('menus', $original_menu_data);

    }

    public function test_user_cannot_update_other_users_menus()
    {
        $other_user = $this->createOtherUser();
        $other_users_menu = $this->createMenu($other_user);
        $original_other_menu_data = $other_users_menu->only(['title', 'content', 'public', 'user_id']);
        $response = $this->actingAs($this->user)
                        ->patch(route('menus.update', $other_users_menu), $this->new_menu_data);
        $response->assertForbidden();
        $this->assertDatabaseMissing('menus', $this->new_menu_data);
        $this->assertDatabaseHas('menus', $original_other_menu_data);
    }

    public function test_guest_cannot_destroy_menus()
    {
        $menu = $this->createMenu($this->user);
        $response = $this->delete(route('menus.destroy', $menu));
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
        ]);
    }

    public function test_user_can_destroy_menus()
    {
        $menu = $this->createMenu($this->user);
        $response = $this->actingAs($this->user)
                        ->delete(route('menus.destroy', $menu));
        $response->assertRedirect(route('menus.index'));
        $response->assertSessionHas('flash_message');
        $this->assertSoftDeleted($menu);
    }

    public function test_user_cannot_destroy_other_users_menus()
    {
        $other_user = $this->createOtherUser();
        $other_users_menu = $this->createMenu($other_user);
        $response = $this->actingAs($this->user)
                        ->delete(route('menus.destroy', $other_users_menu));
        $response->assertForbidden();
        $this->assertDatabaseHas('menus', [
            'id' => $other_users_menu->id,
        ]);
    }

    public function test_user_can_store_menus_with_tags()
    {
        // データの用意
        $existing_tag = Tag::factory()->create(['name' => '既存タグ']);
        $new_tag_name = '新規タグ';
        $input_tags_string = implode(' ', [$existing_tag->name, $new_tag_name]);

        $menu_data_for_store = array_merge(
            $this->new_menu_data,
            ['input_tags' => $input_tags_string]
        );

        $expected_menu_attributes = Arr::except($menu_data_for_store, ['input_tags']);
        $expected_menu_attributes['user_id'] = $this->user->id;
        
        // post
        $response = $this->actingAs($this->user)
                        ->post(route('menus.store'), $menu_data_for_store);

        // アサーション

        // メニューが正しく作られたか
        $response->assertRedirect(route('menus.index'));
        $this->assertDatabaseHas('menus', $expected_menu_attributes);
        $response->assertSessionHas('flash_message');
        $created_menu = Menu::where('title', $expected_menu_attributes['title'])
                            ->where('user_id', $this->user->id)
                            ->latest()
                            ->first();
        $this->assertNotNull($created_menu, 'Menu should be created');

        // 正しく関連付けがされているか
        $this->assertCount(2, $created_menu->tags);
        $this->assertEqualsCanonicalizing(
            [$existing_tag->name, $new_tag_name],
            $created_menu->tags->pluck('name')->all(),
            'Menu should be associated with the correct tags.'
        );

        $this->assertDatabaseHas('tags', ['name' => $new_tag_name]);
    }

    public function test_user_can_update_menu_with_tags_add_remove_and_keep()
    {
        
        // 初期状態の設定
        
        $menu = $this->createMenu($this->user);

        $keep_tag = Tag::factory()->create(['name' => 'keep_tag']);
        $remove_tag = Tag::factory()->create(['name' => 'remove_tag']);
        $new_tag_name = 'new_tag';

        $menu->tags()->attach([$keep_tag->id, $remove_tag->id]);

        // 初期状態の確認
        $menu->refresh();
        $this->assertCount(2, $menu->tags);
        $this->assertEqualsCanonicalizing(
            ['keep_tag', 'remove_tag'],
            $menu->tags->pluck('name')->all()
        );

        // 更新用データ準備
        $update_payload = array_merge(
            Arr::except($this->new_menu_data, 'input_tags'),
            ['input_tags' => implode(' ', [$keep_tag->name, $new_tag_name])]
        );

        // リクエスト実行
        $response = $this->actingAs($this->user)
                        ->patch(route('menus.update', $menu), $update_payload);

        // アサーション

        // ページ処理
        $response->assertRedirect(route('menus.show', $menu));
        $response->assertSessionHas('flash_message');

        // メニューの更新確認
        $menu->refresh();
        $this->assertSame($update_payload['title'], $menu->title);
        $this->assertSame($update_payload['content'], $menu->content);
        $this->assertEquals((bool)$update_payload['public'], (bool)$menu->public);

        // 関連付け
        $this->assertCount(2, $menu->tags, 'Menu should now have 2 tags.');
        $this->assertEqualsCanonicalizing(
            [$keep_tag->name, $new_tag_name],
            $menu->tags->pluck('name')->all(),
            'Menu tags should be updated to keep_tag and new_tag.'
        );

        // タグが作成されたか
        $this->assertDatabaseHas('tags', ['name' => $new_tag_name]);

        // タグが消えていないか
        $this->assertDatabaseHas('tags', ['name' => $remove_tag->name]);
    }

    public function test_guest_cannot_add_menus_to_favorites()
    {
        $menu = $this->createMenu($this->user);
        $response = $this->post(route('menus.favorite', $menu));
        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('menu_favorites', [
            'menu_id' => $menu->id,
        ]);
    }

    public function test_user_can_add_own_menus_to_favorites()
    {
        $menu = $this->createMenu($this->user);
        $response = $this->actingAs($this->user)
                        ->post(route('menus.favorite', $menu));
        $response->assertRedirectBack();
        $this->assertDatabaseHas('menu_favorites', [
            'user_id' => $this->user->id,
            'menu_id' => $menu->id,
        ]);
    }

    public function test_user_can_add_other_users_public_menus_to_favorites()
    {
        $other_user = $this->createOtherUser();
        $menu = $this->createMenu($other_user, [
            'public' => true,
        ]);
        $response = $this->actingAs($this->user)
                        ->post(route('menus.favorite', $menu));
        $response->assertRedirectBack();
        $this->assertDatabaseHas('menu_favorites', [
            'user_id' => $this->user->id,
            'menu_id' => $menu->id,
        ]);
    }

    public function test_user_cannot_add_other_users_private_menus_to_favorites()
    {
        $other_user = $this->createOtherUser();
        $menu = $this->createMenu($other_user, [
            'public' => false,
        ]);
        $response = $this->actingAs($this->user)
                        ->post(route('menus.favorite', $menu));
        $response->assertForbidden();
        $this->assertDatabaseMissing('menu_favorites', [
            'user_id' => $this->user->id,
            'menu_id' => $menu->id,
        ]);
    }

    public function test_user_can_remove_own_menus_from_favorites()
    {
        $menu = $this->createMenu($this->user);
        $this->user->favoriteMenus()->attach($menu);
        $this->assertDatabaseHas('menu_favorites', [
            'user_id' => $this->user->id,
            'menu_id' => $menu->id,
        ]);

        $response = $this->actingAs($this->user)
                        ->post(route('menus.favorite', $menu));
        $response->assertRedirectBack();
        $this->assertDatabaseMissing('menu_favorites', [
            'user_id' => $this->user->id,
            'menu_id' => $menu->id,
        ]);
    }

    public function test_user_can_remove_other_users_menus_from_favorites()
    {
        $other_user = $this->createOtherUser();
        $menu = $this->createMenu($other_user, [
            'public' => true,
        ]);
        $this->user->favoriteMenus()->attach($menu);
        $this->assertDatabaseHas('menu_favorites', [
            'user_id' => $this->user->id,
            'menu_id' => $menu->id,
        ]);

        $response = $this->actingAs($this->user)
                        ->post(route('menus.favorite', $menu));
        $response->assertRedirectBack();
        $this->assertDatabaseMissing('menu_favorites', [
            'user_id' => $this->user->id,
            'menu_id' => $menu->id,
        ]);
    }
}
