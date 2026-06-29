<?php

declare(strict_types=1);

namespace HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures;

enum PostPermission: int
{
    case READ = 1;
    case WRITE = 2;
    case UPDATE = 4;
    case DELETE = 8;
}
