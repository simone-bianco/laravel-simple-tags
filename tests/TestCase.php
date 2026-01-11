<?php

namespace SimoneBianco\LaravelSimpleTags\Tests;

use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use SimoneBianco\LaravelSimpleTags\SimpleTagsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            SimpleTagsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        
        $app['config']->set('tags.taggable.table_name', 'taggables');
    }

    protected function setUpDatabase()
    {
        // Include the migration from the package
        $migration = include __DIR__ . '/../database/migrations/create_tag_tables.php.stub';
        $migration->up();

        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }
}
