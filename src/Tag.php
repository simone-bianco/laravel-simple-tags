<?php

namespace SimoneBianco\LaravelSimpleTags;

use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DbCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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

    public function scopeWithType(Builder $query, ?string $type = null): Builder
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('type', $type);
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
        string | null $type = null
    ): Collection | Tag | static {
        $tags = collect($values)->map(function ($value) use ($type) {
            if ($value instanceof self) {
                return $value;
            }

            return static::findOrCreateFromString($value, $type);
        });

        return is_string($values) ? $tags->first() : $tags;
    }

    public static function getWithType(string $type): DbCollection
    {
        return static::withType($type)->get();
    }

    public static function findFromString(string $name, ?string $type = null)
    {
        // Simple string match.
        // Spatie handles locale here, we don't.
        return static::query()
            ->where('type', $type)
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

    public static function findOrCreateFromString(string $name, ?string $type = null)
    {
        $tag = static::findFromString($name, $type);

        if (! $tag) {
            $tag = static::create([
                'name' => $name,
                'type' => $type,
            ]);
        }

        return $tag;
    }

    public static function getTypes(): Collection
    {
        return static::groupBy('type')->orderBy('type')->pluck('type');
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
