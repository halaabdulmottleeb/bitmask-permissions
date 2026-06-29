<?php

declare(strict_types=1);

namespace HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures;

use HalaAbdulmottleb\BitmaskPermissions\Traits\HasBitmaskPermissions;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasBitmaskPermissions;

    protected $table = 'users';

    protected $guarded = [];
}
