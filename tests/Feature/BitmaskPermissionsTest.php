<?php

use HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\Post;
use HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\PostPermission;
use HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\User;

it('grants a single permission on a resource instance', function (): void {
    $user = User::create(['name' => 'Hala']);
    $post = Post::create(['title' => 'Hello']);
    $user->grantPermissionTo(PostPermission::READ->value, $post);

    expect($user->hasPermissionTo(PostPermission::READ->value, $post))->toBeTrue()
        ->and($user->hasPermissionTo(PostPermission::WRITE->value, $post))->toBeFalse();
});
