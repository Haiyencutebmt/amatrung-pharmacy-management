<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$users = User::all();
foreach ($users as $user) {
    echo "User: {$user->name} (Email: {$user->email}, Role: {$user->role})\n";
    echo "  - Spatie Direct Perms: " . implode(', ', $user->permissions->pluck('name')->toArray()) . "\n";
    echo "  - Spatie Roles: " . implode(', ', $user->roles->pluck('name')->toArray()) . "\n";
    foreach ($user->roles as $r) {
        echo "    - Role {$r->name} Perms: " . implode(', ', $r->permissions->pluck('name')->toArray()) . "\n";
    }
    echo "  - can('manage_inventory'): " . ($user->can('manage_inventory') ? 'Yes' : 'No') . "\n";
    echo "  - hasPermission('manage_inventory'): " . ($user->hasPermission('manage_inventory') ? 'Yes' : 'No') . "\n";
    echo "\n";
}
