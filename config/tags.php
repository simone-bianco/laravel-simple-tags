<?php

return [
    /*
     * The fully qualified class name of the tag model.
     */
    'tag_model' => SimoneBianco\LaravelSimpleTags\Tag::class,

    /*
     * The name of the table associated with the taggable morph relation.
     */
    'taggable' => [
        'table_name' => 'taggables',
        'morph_name' => 'taggable',

        /*
         * The fully qualified class name of the pivot model.
         */
        'class_name' => Illuminate\Database\Eloquent\Relations\MorphPivot::class,
    ]
];
