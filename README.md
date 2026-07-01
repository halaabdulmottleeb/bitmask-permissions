# Bitmask Permissions

A lightweight, high-performance authorization package for Laravel that stores permissions as **integer bitmasks** instead of relational pivot tables.

Every permission a subject holds on a resource is packed into a single integer column, so checks are a single indexed query with a bitwise `AND` — no joins, no pivot-row lookups. It supports both **object-level** (per-record) and **global** (resource-wide) grants out of the box.

## Why bitmasks?

Traditional permission packages store each grant as one or more rows across several pivot tables. This package stores all of a subject's permissions on a given resource in one `BIGINT`:

| | Pivot-table approach | Bitmask (this package) |
| --- | --- | --- |
| Storage per grant | multiple rows across pivot tables | a single integer column |
| Permission check | row lookups / joins | one bitwise `AND` in a single indexed query |
| Object-level permissions | not first-class | native (`resource_type` + nullable `resource_id`) |
| Combining permissions | insert / delete rows | bitwise `OR` / `AND` |

**Trade-off:** the number of distinct permissions per resource type is capped by the integer width (64 with `BIGINT`), and there are no built-in roles/caching helpers. It's optimized for fast, fine-grained, per-resource checks.

## Requirements

- PHP `^8.2`
- Laravel 10, 11, or 12 (`illuminate/database`, `illuminate/support`)

## Installation

```bash
composer require hala-abdulmottleb/bitmask-permissions
```

The service provider is auto-discovered. Publish and run the migration:

```bash
php artisan vendor:publish --tag=bitmask-permissions-migrations
php artisan migrate
```

Optionally publish the config file to change the table name:

```bash
php artisan vendor:publish --tag=bitmask-permissions-config
```

## Usage

### 1. Define your permissions as bit values

Each permission is a single bit — use powers of two. An enum is a clean way to do this:

```php
enum PostPermission: int
{
    case READ   = 1;   // 0001
    case WRITE  = 2;   // 0010
    case UPDATE = 4;   // 0100
    case DELETE = 8;   // 1000
}
```

### 2. Add the trait to any model that can hold permissions

```php
use HalaAbdulmottleb\BitmaskPermissions\Traits\HasBitmaskPermissions;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasBitmaskPermissions;
}
```

Any Eloquent model works as the **subject** (User, Team, ApiClient, ...) because the relation is polymorphic.

### 3. Grant, check, and revoke

```php
$user->grantPermissionTo(PostPermission::READ->value, $post);
$user->grantPermissionTo(PostPermission::WRITE->value, $post);

$user->hasPermissionTo(PostPermission::READ->value, $post);  // true
$user->hasPermissionTo(PostPermission::DELETE->value, $post); // false

$user->revokePermission(PostPermission::WRITE->value, $post);
```

### Combining permissions

Because permissions are bits, you can grant several at once with a bitwise `OR`:

```php
$user->grantPermissionTo(
    PostPermission::READ->value | PostPermission::WRITE->value,
    $post
);
```

`hasPermissionTo()` checks that **all** requested bits are present (`(mask & permission) = permission`).

### Global (resource-wide) grants

The `resource_id` column is nullable. A grant with a null `resource_id` applies to **every** record of that resource type, and `hasPermissionTo()` matches either the specific record or the global grant.

## API

The `HasBitmaskPermissions` trait adds:

| Method | Description |
| --- | --- |
| `grantPermissionTo(int $permission, Model $resource)` | Adds the permission bit(s) for the subject on the resource. |
| `hasPermissionTo(int $permission, Model $resource): bool` | Returns `true` if the subject has all requested bits (record-specific or global). |
| `revokePermission(int $permission, Model $resource)` | Clears the permission bit(s) for the subject on the resource. |
| `grantPermissions(): MorphMany` | The underlying polymorphic relation to `PermissionGrant`. |

## Configuration

`config/bitmask-permissions.php`:

```php
return [
    'table' => 'permission_grants',
];
```

## Schema

A single table holds everything:

- `subject_type` / `subject_id` — polymorphic owner of the permissions
- `resource_type` — the resource class (via morph map)
- `resource_id` — nullable; `null` means a global grant for that resource type
- `mask` — the bitmask of granted permissions
- unique on `(subject_type, subject_id, resource_type, resource_id)` — one row per subject–resource pair

## Testing

```bash
composer install
vendor/bin/pest
```

Tests run against an in-memory SQLite database via Orchestra Testbench.

## License

MIT © Hala Abdulmottleb
