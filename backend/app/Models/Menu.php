<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'public',
    ];

    public const SORT_LIST = [
        '作成日の新しい順' => ['column' => 'created_at', 'direction' => 'desc'],
        '作成日の古い順' => ['column' => 'created_at', 'direction' => 'asc'],
        '更新日の新しい順' => ['column' => 'updated_at', 'direction' => 'desc'],
        '更新日の古い順' => ['column' => 'updated_at', 'direction' => 'asc'],
        'お気に入り数の多い順' => ['column' => 'favorited_users_count', 'direction' => 'desc'],
        'メモ作成日の新しい順' => ['column' => 'memo_created_at', 'direction' => 'desc'],
        'メモ作成日の古い順' => ['column' => 'memo_created_at', 'direction' => 'asc'],
        'メモ更新日の新しい順' => ['column' => 'memo_updated_at', 'direction' => 'desc'],
        'メモ更新日の古い順' => ['column' => 'memo_updated_at', 'direction' => 'asc'],
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
                    ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoritedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'menu_favorites', 'menu_id', 'user_id')
                    ->withTimestamps();
    } 

    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class);
    }

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class);
    }

    /**
     * キーワードでメニューを検索するスコープ。
     * 対象：タイトル、本文、作者名、タグ名
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByKeyword($query, $keyword_str)
    {
        if (empty($keyword_str)) {
            return $query;
        }

        $keywords = preg_split('/\s+/u', $keyword_str, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($keywords as $keyword) {
            $escaped_keyword = addcslashes($keyword, '%_\\');
            $query->where(function ($q) use ($escaped_keyword) {
                $q->where('title', 'like', "%{$escaped_keyword}%")
                    ->orWhere('content', 'like', "%{$escaped_keyword}%")
                    ->orWhereHas('user', function ($sub_query) use ($escaped_keyword) {
                        $sub_query->where('name', 'like', "%{$escaped_keyword}%");
                    })
                    ->orWhereHas('tags', function ($sub_query) use ($escaped_keyword) {
                        $sub_query->where('name', 'like', "%{$escaped_keyword}%");
                    });
            });   
        }
        return $query;
    }

    /**
     * 作者名でメニューを絞り込むスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $author_name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByAuthor($query, $author_name)
    {
        if (empty($author_name)) {
            return $query;
        }

        return $query->whereHas('user', function ($q) use ($author_name) {
            $q->where('name', $author_name);
        });
    }

    /** 
     * 絞り込みボタンから選択されたタグで絞り込み検索をするスコープ
     *  
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int[] $tag_ids
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByTagIds($query, $tag_ids)
    {
        if (empty($tag_ids)) {
            return $query;
        }

        return $query->whereHas('tags', function ($q) use ($tag_ids) {
            $q->whereIn('tags.id', $tag_ids);
        }, '=', count($tag_ids));
    }

    /**
     * 作者が自分のメニュー/公開設定がONのメニューに絞り込むためのスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByPublic($query, $user_id)
    {
        return $query->where(function ($q) use ($user_id) {
                $q->where('menus.user_id', $user_id)
                    ->orWhere('menus.public', true);
        });
    }

    /**
     * お気に入りに追加しているメニューに絞り込むためのスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param boolean $is_only_favorited
     * @param int|null $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeFilterByFavorited($query, $is_only_favorited, $user_id)
    {
    if (!$is_only_favorited) {
        return $query;
    }

    return $query->whereHas('favoritedUsers', function ($q) use ($user_id) {
        $q->where('menu_favorites.user_id', $user_id);
    });
    }

    /**
     * 条件に応じた並べ替えをするためのスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  string $sort_type
     * @param int|null $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByConditions($query, $sort_type, $user_id) {

        if (!$sort_type) {
            return $query;
        }

        $sort_column = self::SORT_LIST[$sort_type]['column'];
        $sort_direction = self::SORT_LIST[$sort_type]['direction'];

        if ($sort_column === 'memo_created_at' || $sort_column === 'memo_updated_at') {
            $subquery_column = str_replace('memo_', '', $sort_column);
            // サブクエリを使って各メニューに紐づくログイン中ユーザーのメモの日時を取得し、
            // $sort_columnという名前でSELECT結果に含める
            $query->addSelect([$sort_column => Memo::select($subquery_column)
                                                        ->whereColumn('menus.id', 'memos.menu_id')
                                                        ->where('user_id', $user_id)
                                                        ->limit(1) // 仕様上は不要だが、サブクエリが単一の値を返すのを保証し、DBエラーを起こさないための防御的プログラミングとして。
            ]);
            $query->orderByRaw("{$sort_column} IS NULL ASC");
        }

        return $query->orderBy($sort_column, $sort_direction);
    }

    

    /**
     * 関連付けられたタグをスペース区切りの文字列として取得するアクセサ
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute 
     */
    protected function formattedTags(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tags->pluck('name')->implode(' '),
        );
    }
}
