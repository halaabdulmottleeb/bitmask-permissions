<?php

declare(strict_types=1);

namespace HalaAbdulmottleb\BitmaskPermissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PermissionGrant extends Model
{
    protected $guarded = [];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function resource(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTableName(): string
    {
        return config('bitmask-permissions.table', 'permission_grants');
    }
}
