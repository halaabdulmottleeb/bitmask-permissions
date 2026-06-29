<?php

use \HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\PostPermission;



it('grants a single permission on a resource instance', function (): void {
    $user = \HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\User::create(['name' => 'Hala']);
    $post = \HalaAbdulmottleb\BitmaskPermissions\Tests\Fixtures\Post::create(['title' => 'Hello']);
    $user->grantPermissionTo(PostPermission::READ->value, $post);

    expect($user->hasPermissionTo(PostPermission::READ->value, $post))->toBeTrue()
        ->and($user->hasPermissionTo(PostPermission::WRITE->value, $post))->toBeFalse();
});
