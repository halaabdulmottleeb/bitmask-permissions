<?php

declare(strict_types=1);

namespace HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $guarded = [];
}
