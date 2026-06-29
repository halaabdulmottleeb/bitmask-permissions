<?php

declare(strict_types=1);

namespace HalaAbdulmottleb\BitmaskPermissions\traits;

use HalaAbdulmottleb\BitmaskPermissions\Models\PermissionGrant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBitmaskPermissions
{
    public function grantPermissions(): MorphMany
    {
        return $this->morphMany(PermissionGrant::class, 'subject');
    }

    public function hasPermissionTo(int $permission, Model $resource): bool
    {
        return $this->grantPermissions()
            ->where('resource_type', $resource->getMorphClass())
            ->where(function ($query) use ($resource) {
                $query->where('resource_id', $resource->id)
                    ->orWhere('resource_id', null);
            })
            ->whereRaw('(mask & ?) = ?', [$permission, $permission])
            ->exists();
    }

    public function grantPermissionTo(int $permission, Model $resource)
    {
        $grant = $this->grantPermissions()->firstOrNew([
            'resource_id' => $resource->id,
            'resource_type' => $resource->getMorphClass(),
        ]);

        $grant->mask = ($grant->mask ?? 0) | $permission;

        $grant->save();
    }

    public function revokePermission(int $permission, Model $resource)
    {
        $grant = $this->grantPermissions()
            ->where('resource_id', $resource->id)
            ->where('resource_type', $resource->type)->first();

        $grant->mask &= ~$permission;
    }
}
