<?php

namespace SimoneBianco\LaravelSimpleTags\Tests;

use SimoneBianco\LaravelSimpleTags\Tag;

class TagTest extends TestCase
{
    /** @test */
    public function it_can_create_a_tag()
    {
        $tag = Tag::findOrCreate('test');

        $this->assertEquals('test', $tag->name);
        $this->assertEquals('test', $tag->slug);
        $this->assertNull($tag->type);
    }

    /** @test */
    public function it_can_create_a_tag_with_type()
    {
        $tag = Tag::findOrCreate('test', 'type1');

        $this->assertEquals('test', $tag->name);
        $this->assertEquals('type1', $tag->type);
    }

    /** @test */
    public function it_can_find_a_tag_from_string()
    {
        Tag::create(['name' => 'test']);

        $tag = Tag::findFromString('test');

        $this->assertNotNull($tag);
        $this->assertEquals('test', $tag->name);
    }

    /** @test */
    public function it_generates_slugs()
    {
        $tag = Tag::create(['name' => 'Test Tag']);
        $this->assertEquals('test-tag', $tag->slug);
    }

    /** @test */
    public function it_can_find_or_create_multiple_tags()
    {
        $tags = Tag::findOrCreate(['tag1', 'tag2']);

        $this->assertCount(2, $tags);
        $this->assertEquals('tag1', $tags[0]->name);
        $this->assertEquals('tag2', $tags[1]->name);
    }
}
