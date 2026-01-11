<?php

namespace SimoneBianco\LaravelSimpleTags\Tests;

use Illuminate\Database\Eloquent\Model;
use SimoneBianco\LaravelSimpleTags\HasTags;

class TestModel extends Model
{
    use HasTags;

    public $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;
}
