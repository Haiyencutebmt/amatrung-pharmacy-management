<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('role')->orderByDesc('created_at')->paginate(20);

        $totalUsers = User::count();
        $adminCount = User::where('role', 'admin')->count();
        $staffCount = User::where('role', 'staff')->count();
        $activeCount = User::where('is_active', 1)->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'adminCount', 'staffCount', 'activeCount'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff',
            'permissions' => 'nullable|array',
        ]);

        $permissions = $validated['permissions'] ?? [];
        unset($validated['permissions']);

        if ($validated['role'] === 'admin') {
            $permissions = [];
        }

        $validated['legacy_permissions_json'] = $permissions;
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = 1;

        $user = User::create($validated);

        $user->assignRole($validated['role']);
        if ($validated['role'] === 'staff') {
            $validPerms = \Spatie\Permission\Models\Permission::whereIn('name', $permissions)->pluck('name')->toArray();
            $user->syncPermissions($validPerms);
        }

        return redirect()->route('admin.users.index')->with('success', 'Đã tạo tài khoản thành công.');
    }

    public function edit(User $user)
    {
        if ($user->role === 'user') {
            return redirect()->route('admin.users.index')->with('error', 'Không được phép thay đổi tài khoản của khách hàng.');
        }
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'user') {
            return redirect()->route('admin.users.index')->with('error', 'Không được phép thay đổi tài khoản của khách hàng.');
        }

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không thể tự hạ quyền của chính mình.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,staff',
            'permissions' => 'nullable|array',
        ]);

        $permissions = $validated['permissions'] ?? [];
        unset($validated['permissions']);

        if ($validated['role'] === 'admin') {
            $permissions = []; // Admin has all permissions intrinsically
        }

        $validated['legacy_permissions_json'] = $permissions;
        $user->update($validated);

        // Sync Spatie Role and Permissions
        $user->syncRoles([$validated['role']]);
        
        // Remove existing direct permissions and assign new ones
        if ($validated['role'] === 'staff') {
            // Find valid permissions in Spatie
            $validPerms = \Spatie\Permission\Models\Permission::whereIn('name', $permissions)->pluck('name')->toArray();
            $user->syncPermissions($validPerms);
        } else {
            $user->syncPermissions([]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Đã cập nhật thông tin tài khoản.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'user') {
            return redirect()->route('admin.users.index')->with('error', 'Không được phép khóa/mở khóa tài khoản của khách hàng.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $statusStr = $user->is_active ? 'mở khóa' : 'khóa';
        return redirect()->back()->with('success', "Đã {$statusStr} tài khoản.");
    }

    public function resetPassword(User $user)
    {
        if ($user->role === 'user') {
            return redirect()->route('admin.users.index')->with('error', 'Không được phép đặt lại mật khẩu tài khoản của khách hàng.');
        }

        $user->update(['password' => Hash::make('amatrung@123')]);
        return redirect()->back()->with('success', "Đã đặt lại mật khẩu thành 'amatrung@123'.");
    }

    public function destroy(User $user)
    {
        if ($user->role === 'user') {
            return redirect()->route('admin.users.index')->with('error', 'Không được phép xóa tài khoản của khách hàng.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Đã xóa tài khoản nhân viên thành công.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một tài khoản để xóa.');
        }

        // Only allow deleting staff accounts
        $deleted = User::whereIn('id', $ids)
            ->where('role', 'staff')
            ->where('id', '!=', auth()->id())
            ->delete();

        if ($deleted > 0) {
            return redirect()->back()->with('success', "Đã xóa thành công $deleted tài khoản nhân viên.");
        }

        return redirect()->back()->with('error', 'Không có tài khoản nào được xóa (chỉ có thể xóa tài khoản nhân viên).');
    }
}
