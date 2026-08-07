<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Mail\UserCredentialsMail;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-user')->only(['index', 'show']);
        $this->middleware('permission:create-user')->only(['create', 'store']);
        $this->middleware('permission:edit-user')->only(['edit', 'update']);
        $this->middleware('permission:delete-user')->only(['destroy']);
    }
    
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('roles')
            ->filter(['search' => $search])
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'created_at' => $user->created_at?->format('d M Y'),
            ]);

        return Inertia::render('User/Index', [
            'users' => $users,
            'searchTerm' => $search ?? '',
        ]);
    }

    public function create()
    {
        return Inertia::render('User/Create', [
            'roles' => $this->roleOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'exists:roles,id'],
        ]);
    
        $plainPassword = Str::password(12);
    
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($plainPassword),
            'email_verified_at' => now(),
        ]);
    
        $roleName = Role::where('id', $validated['role'])->value('name');
    
        $user->assignRole($roleName);
    
        Mail::to($user->email)->send(
            new UserCredentialsMail($user, $plainPassword)
        );
    
        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'User created successfully. Login credentials have been sent to their email.'
            );
    }

    public function edit(User $user)
    {
        $user->load('roles');

        return Inertia::render('User/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_ids' => $user->roles->pluck('id')->toArray(),
            ],
            'roles' => $this->roleOptions(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,id'],
        ]);
    
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];
    
        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }
    
        $user->update($data);
    
        $roleName = Role::where('id', $validated['role'])->value('name');
    
        $user->syncRoles([$roleName]);
    
        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function roleOptions(): array
    {
        return Role::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
            ])
            ->all();
    }
}
