<?php

declare(strict_types=1);

namespace HalaAbdulmottleb\BitmaskPermissions\Traits;

use HalaAbdulmottleb\BitmaskPermissions\Models\PermissionGrant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Model
 */
trait HasBitmaskPermissions
{
    /**
     * @return MorphMany<PermissionGrant, $this>
     */
    public function grantPermissions(): MorphMany
    {
        return $this->morphMany(PermissionGrant::class, 'subject');
    }

    public function hasPermissionTo(int $permission, Model $resource): bool
    {
        return $this->grantPermissions()
            ->where('resource_type', $resource->getMorphClass())
            ->where(function ($query) use ($resource) {
                $query->where('resource_id', $resource->getKey())
                    ->orWhere('resource_id', null);
            })
            ->whereRaw('(mask & ?) = ?', [$permission, $permission])
            ->exists();
    }

    public function grantPermissionTo(int $permission, Model $resource): void
    {
        /**
         * @var PermissionGrant $grant
         */
        $grant = $this->grantPermissions()->firstOrNew([
            'resource_id' => $resource->getKey(),
            'resource_type' => $resource->getMorphClass(),
        ]);

        $grant->mask |= $permission;

        $grant->save();
    }

    public function revokePermission(int $permission, Model $resource): void
    {
        /**
         * @var PermissionGrant $grant
         */
        $grant = $this->grantPermissions()
            ->where('resource_id', $resource->getKey())
            ->where('resource_type', $resource->getMorphClass())->first();

        $grant->mask &= ~$permission;
        $grant->save();
    }
}
