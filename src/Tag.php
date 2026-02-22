<?php

namespace SimoneBianco\LaravelSimpleTags;

use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DbCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Tag extends Model
{
    public $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::saving(function (Tag $tag) {
            $tag->slug = Str::slug($tag->slug);
            if (empty($tag->name)) {
                $tag->name = $tag->slug;
            }
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function tagType(): BelongsTo
    {
        $tagTypeModel = config('tags.tag_type_model', TagType::class);
        return $this->belongsTo($tagTypeModel, 'tag_type_id');
    }

    public function scopeWithType(Builder $query, ?int $tagTypeId = null): Builder
    {
        if (is_null($tagTypeId)) {
            return $query;
        }

        return $query->where('tag_type_id', $tagTypeId);
    }

    public function scopeWithTypeAlias(Builder $query, ?string $alias = null): Builder
    {
        if (is_null($alias)) {
            return $query;
        }

        return $query->whereHas('tagType', fn ($q) => $q->where('alias', $alias));
    }

    public function scopeContaining(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }

    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('order_column', $direction);
    }

    public static function findOrCreate(
        string | array | ArrayAccess $values,
        int | null $tagTypeId = null
    ): Collection | Tag | static {
        $tags = collect($values)->map(function ($value) use ($tagTypeId) {
            if ($value instanceof self) {
                return $value;
            }

            return static::findOrCreateFromString($value, $tagTypeId);
        });

        return is_string($values) ? $tags->first() : $tags;
    }

    public static function getWithType(int $tagTypeId): DbCollection
    {
        return static::withType($tagTypeId)->get();
    }

    public static function findFromString(string $name, ?int $tagTypeId = null)
    {
        return static::query()
            ->where('tag_type_id', $tagTypeId)
            ->where(function ($query) use ($name) {
                $query->where('name', $name)
                    ->orWhere('slug', $name);
            })
            ->first();
    }

    public static function findFromStringOfAnyType(string $name)
    {
        return static::query()
            ->where('name', $name)
            ->orWhere('slug', $name)
            ->get();
    }

    public static function findOrCreateFromString(string $name, ?int $tagTypeId = null)
    {
        $tag = static::findFromString($name, $tagTypeId);

        if (! $tag) {
            $tag = static::create([
                'name'        => $name,
                'tag_type_id' => $tagTypeId,
            ]);
        }

        return $tag;
    }

    public static function getTypes(): Collection
    {
        return static::query()
            ->join('tag_types', 'tags.tag_type_id', '=', 'tag_types.id')
            ->orderBy('tag_types.alias')
            ->pluck('tag_types.alias')
            ->unique();
    }

    public function getTaggableCountAttribute(): int
    {
        return DB::table('taggables')
            ->where('tag_id', $this->id)
            ->count();
    }

    public function scopeWithTaggableCount($query)
    {
        return $query->addSelect([
            'taggable_count' => DB::table('taggables')
                ->selectRaw('count(*)')
                ->whereColumn('tag_id', 'tags.id')
        ]);
    }
}
