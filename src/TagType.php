<?php

namespace SimoneBianco\LaravelSimpleTags;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagType extends Model
{
    public $guarded = [];

    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('order_column', $direction);
    }

    public function tags(): HasMany
    {
        $tagModel = config('tags.tag_model', Tag::class);
        return $this->hasMany($tagModel, 'tag_type_id');
    }
}
