<?php

declare(strict_types=1);

namespace HalaAbdulmottleb\BitmaskPermissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $mask
 */
class PermissionGrant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'mask' => 'integer',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function resource(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTableName(): string
    {
        return config()->string('bitmask-permissions.table', 'permission_grants');
    }
}
