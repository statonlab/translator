<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responds;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use Responds;

    /**
     * List Users.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('lists', User::class);

        $users = User::with('role');

        if ($request->search) {
            $users->whereHas('role', function ($query) use ($request) {
                $query->where('roles.name', 'like', '%'.$request->search.'%');
            });

            $users->orWhere(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
                $query->orwhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $users = $users->orderBy('users.name', 'asc')->paginate(20);

        $users->getCollection()->transform(function ($user) {
            /** @var User $user */
            $user->registered_at = $user->created_at->diffForHumans();

            return $user;
        });

        return $this->success($users);
    }

    /**
     * Create a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->authorize('create', User::class);

        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|confirmed|max:255|min:6',
            'role' => 'nullable|in:Admin,User',
        ]);

        $user = User::create([
            'name' => $request->name,
            'password' => \Hash::make($request->password),
            'email' => $request->email,
            'role_id' => Role::where('name', $request->role ?: 'User')->id,
        ]);

        $user->load('role');

        return $this->created($user);
    }

    /**
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(User $user)
    {
        $this->authorize('delete', $user);

        try {
            $user->delete();
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }

        return $this->deleted($user);
    }

    /**
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function patch(User $user, Request $request)
    {
        $this->authorize('update', $user);

        $this->validate($request, [
            'name' => 'nullable|max:100',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|max:255|min:6',
            'role' => 'nullable|in:Admin,User',
        ]);

        $user->fill($request->only([
            'name',
            'email',
        ]));

        if (! empty($request->password)) {
            $user->fill([
                'password' => \Hash::make($request->password),
            ]);
        }

        if (! empty($request->role)) {
            $role = Role::where('name', $request->role)->first();

            if ($role) {
                $user->fill([
                    'role_id' => $role->id,
                ]);
            }
        }

        $user->save();

        $user->load('role');

        return $this->created($user);
    }
}
