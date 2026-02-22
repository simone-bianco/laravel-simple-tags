<?php

return [
    'tag_model' => SimoneBianco\LaravelSimpleTags\Tag::class,
    'tag_type_model' => SimoneBianco\LaravelSimpleTags\TagType::class,

    'taggable' => [
        'table_name' => 'taggables',
        'morph_name' => 'taggable',
        'class_name' => Illuminate\Database\Eloquent\Relations\MorphPivot::class,
    ]
];
