<?php

namespace SimoneBianco\LaravelSimpleTags;

use Illuminate\Support\ServiceProvider;

class SimpleTagsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/tags.php' => config_path('tags.php'),
            ], 'tags-config');

            if (! class_exists('CreateTagTables')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_tag_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tag_tables.php'),
                ], 'tags-migrations');
            }
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tags.php', 'tags');
    }
}
