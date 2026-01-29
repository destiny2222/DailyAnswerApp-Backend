# Role & Permission System

This application uses **Spatie Laravel Permission** for comprehensive role-based access control across all admin controllers.

## Setup

```bash
# Install Spatie Permission (if not already installed)
composer require spatie/laravel-permission

# Publish config and migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RolePermissionSeeder
```

## Roles

| Role | Description |
|------|-------------|
| **Publisher** | Super admin with full access to everything |
| **Editor** | Can manage content but cannot publish or manage roles |
| **Writer** | Can create and edit own devotionals, submit for review |
| **Viewer** | Read-only access to content |

## Permissions

### Devotionals
- `view devotionals` - View all devotionals
- `view own devotionals` - View only own devotionals
- `create devotionals` - Create new devotionals
- `edit devotionals` - Edit any devotional
- `edit own devotionals` - Edit only own devotionals
- `delete devotionals` - Delete devotionals
- `publish devotionals` - Publish devotionals
- `unpublish devotionals` - Unpublish devotionals
- `submit for review devotionals` - Submit for review

### Users
- `view users` - View user list
- `create users` - Create new users
- `edit users` - Edit users
- `delete users` - Delete users
- `ban users` - Ban users
- `unban users` - Unban users

### Admins
- `view admins` - View admin list
- `create admins` - Create new admins
- `edit admins` - Edit admins
- `delete admins` - Delete admins
- `assign roles admins` - Assign roles to admins

### Roles & Permissions
- `view roles` - View role list
- `create roles` - Create new roles
- `edit roles` - Edit roles
- `delete roles` - Delete roles
- `assign permissions roles` - Assign permissions to roles
- `view permissions` - View permission list
- `create permissions` - Create new permissions
- `edit permissions` - Edit permissions
- `delete permissions` - Delete permissions

### User Content
- `view notes` - View own user notes
- `view all notes` - View all user notes
- `delete notes` - Delete user notes
- `view prayer-notes` - View own prayer notes
- `view all prayer-notes` - View all prayer notes
- `delete prayer-notes` - Delete prayer notes
- `view memory-verses` - View own memory verses
- `view all memory-verses` - View all memory verses
- `delete memory-verses` - Delete memory verses

## Usage in Controllers

### Method 1: Using BaseAdminController

Extend `BaseAdminController` and call `setPermissions()` in constructor:

```php
use App\Http\Controllers\Admin\BaseAdminController;

class MyController extends BaseAdminController
{
    public function __construct()
    {
        // Automatically sets: view, create, edit, delete permissions
        $this->setPermissions('resource-name');
    }
}
```

### Method 2: Custom Middleware

Define custom permissions in constructor:

```php
class DevotionalController extends BaseAdminController
{
    public function __construct()
    {
        $this->middleware('permission:view devotionals|view own devotionals', ['only' => ['index']]);
        $this->middleware('permission:create devotionals', ['only' => ['create', 'store']]);
        $this->middleware('permission:publish devotionals', ['only' => ['publish']]);
    }
}
```

### Method 3: Using authorize() in Methods

```php
public function publish(Devotional $devotional)
{
    $this->authorize('publish devotionals');
    
    // Your logic here
}
```

### Method 4: Using Gates in Views

```blade
@can('create devotionals')
    <a href="{{ route('admin.devotionals.create') }}" class="btn btn-primary">
        Create Devotional
    </a>
@endcan

@can('edit devotionals')
    <a href="{{ route('admin.devotionals.edit', $devotional) }}" class="btn btn-secondary">
        Edit
    </a>
@endcan
```

## Checking Permissions in Code

```php
// Check single permission
if (auth('admin')->user()->can('create devotionals')) {
    // User has permission
}

// Check any permission
if (auth('admin')->user()->hasAnyPermission(['edit devotionals', 'edit own devotionals'])) {
    // User has at least one permission
}

// Check all permissions
if (auth('admin')->user()->hasAllPermissions(['view users', 'edit users'])) {
    // User has all permissions
}

// Check role
if (auth('admin')->user()->hasRole('publisher')) {
    // User is a publisher
}
```

## Form Request Authorization

```php
class StoreDevotionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() && auth('admin')->user()->can('create devotionals');
    }
}
```

## Assigning Roles to Admins

```php
use App\Models\Admin;

$admin = Admin::find(1);

// Assign role
$admin->assignRole('writer');

// Sync roles (removes other roles)
$admin->syncRoles(['editor']);

// Remove role
$admin->removeRole('viewer');

// Give permission directly
$admin->givePermissionTo('create devotionals');
```

## Route Protection

Protect routes in `routes/admin.php`:

```php
Route::middleware(['auth:admin'])->group(function () {
    // Only publishers can access
    Route::middleware(['permission:publish devotionals'])->group(function () {
        Route::post('/devotionals/{devotional}/publish', [DevotionalController::class, 'publish']);
    });
    
    // Multiple permissions (any)
    Route::middleware(['permission:edit devotionals|edit own devotionals'])->group(function () {
        Route::get('/devotionals/{devotional}/edit', [DevotionalController::class, 'edit']);
    });
});
```

## Adding New Permissions

1. Add permission in `RolePermissionSeeder.php`:
```php
Permission::create(['name' => 'export users', 'guard_name' => 'admin']);
```

2. Assign to roles:
```php
$publisher->givePermissionTo('export users');
```

3. Re-run seeder:
```bash
php artisan db:seed --class=RolePermissionSeeder
```

Or create/assign permissions dynamically in code.
