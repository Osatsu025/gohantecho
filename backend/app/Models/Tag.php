<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];
    
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class)
                    ->withTimestamps();
    }

    /**
     * スペース区切りのタグ名文字列からタグを検索または作成し、IDの配列を返す
     * 
     * @param string $tag_names_str
     * @return int[]
     */
    public static function findOrCreateByName(string $tag_names_str): array
    {
        return DB::transaction(function () use ($tag_names_str) {
            $tag_names = array_values(array_unique(preg_split('/\s+/u', $tag_names_str, -1, PREG_SPLIT_NO_EMPTY)));

            if (empty($tag_names)) {
                return [];
            }

            $existing_tags = self::withTrashed()->whereIn('name', $tag_names)->get();
            $existing_tag_names = $existing_tags->pluck('name')->all();

            $tags_to_create = array_diff($tag_names, $existing_tag_names);

            if (!empty($tags_to_create)) {
                $new_tags_data = [];
                $now = now();
                foreach ($tags_to_create as $tag_name) {
                    $new_tags_data[] = ['name' => $tag_name, 'created_at' => $now, 'updated_at' => $now];
                }
                self::insert($new_tags_data);
            }

            $tags_to_restore = $existing_tags->whereNotNull('deleted_at');
            if ($tags_to_restore->isNotEmpty()) {
                $tags_to_restore->toQuery()->restore();
            }

            return self::whereIn('name', $tag_names)->pluck('id')->all();
        });
    }
}
