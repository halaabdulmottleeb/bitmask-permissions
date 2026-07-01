<?php

use HalaAbdulmottleb\BitmaskPermissions\Models\PermissionGrant;
use HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\Post;
use HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\PostPermission;
use HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\User;

it('grants a single permission on a resource instance', function (): void {
    $user = User::query()->create(['name' => 'first user']);
    $post = Post::query()->create(['title' => 'Post Title']);
    $user->grantPermissionTo(PostPermission::READ->value, $post);

    expect($user->hasPermissionTo(PostPermission::READ->value, $post))->toBeTrue()
        ->and($user->hasPermissionTo(PostPermission::WRITE->value, $post))->toBeFalse();
});

it('revoke permission from a resource instance', function (): void {
    $user = User::query()->create(['name' => 'Hala']);
    $post = Post::query()->create(['title' => 'Hello']);

   PermissionGrant::query()->create([
        'subject_id' => $user->id,
        'subject_type' => (new User())->getMorphClass(),
        'resource_id' => $post->id,
        'resource_type' => (new Post())->getMorphClass(),
        'mask' => PostPermission::READ->value,
    ]);
    $user->revokePermission(PostPermission::READ->value, $post);
    expect($user->hasPermissionTo(PostPermission::READ->value, $post))->toBeFalse();
});

