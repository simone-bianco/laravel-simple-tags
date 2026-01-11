<?php

namespace SimoneBianco\LaravelSimpleTags\Tests;

use SimoneBianco\LaravelSimpleTags\Tag;

class HasTagsTest extends TestCase
{
    protected TestModel $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testModel = TestModel::create(['name' => 'testModel']);
    }

    /** @test */
    public function it_can_attach_a_tag()
    {
        $this->testModel->attachTag('test');

        $this->assertCount(1, $this->testModel->tags);
        $this->assertEquals('test', $this->testModel->tags[0]->name);
    }

    /** @test */
    public function it_can_attach_multiple_tags()
    {
        $this->testModel->attachTags(['tag1', 'tag2']);

        $this->assertCount(2, $this->testModel->tags);
        $this->assertEquals('tag1', $this->testModel->tags[0]->name);
        $this->assertEquals('tag2', $this->testModel->tags[1]->name);
    }

    /** @test */
    public function it_can_detach_a_tag()
    {
        $this->testModel->attachTags(['tag1', 'tag2']);
        $this->testModel->detachTag('tag1');

        $this->assertCount(1, $this->testModel->tags);
        $this->assertEquals('tag2', $this->testModel->tags[0]->name);
    }

    /** @test */
    public function it_can_sync_tags()
    {
        $this->testModel->attachTag('tag1');
        $this->testModel->syncTags(['tag2', 'tag3']);

        $this->assertCount(2, $this->testModel->tags);
        $this->assertEquals('tag2', $this->testModel->tags[0]->name);
        $this->assertEquals('tag3', $this->testModel->tags[1]->name);
    }

    /** @test */
    public function it_can_retrieve_models_with_any_tags()
    {
        $this->testModel->attachTags(['tag1', 'tag2']);

        $model2 = TestModel::create(['name' => 'model2']);
        $model2->attachTag('tag3');

        $found = TestModel::withAnyTags(['tag1'])->get();
        $this->assertCount(1, $found);
        $this->assertEquals($this->testModel->id, $found[0]->id);
    }

    /** @test */
    public function it_can_retrieve_models_with_all_tags()
    {
        $this->testModel->attachTags(['tag1', 'tag2']);

        $model2 = TestModel::create(['name' => 'model2']);
        $model2->attachTag('tag1');

        $found2 = TestModel::withAllTags(['tag1', 'tag2'])->get();
        $this->assertCount(1, $found2);
        $this->assertEquals($this->testModel->id, $found2[0]->id);
        
        $found1 = TestModel::withAllTags(['tag1'])->get();
        $this->assertCount(2, $found1);
    }
}
