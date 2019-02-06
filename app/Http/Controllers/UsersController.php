<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responds;
use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $this->validate($request, [
            'search' => 'nullable|max:255',
            'limit' => 'nullable|int|min:6|max:100',
        ]);

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

        $users = $users->orderBy('users.name', 'asc')->paginate($request->limit ?: 20);

        $users->getCollection()->transform(function ($user) {
            /** @var User $user */
            $user->registered_at = $user->created_at->diffForHumans();

            return $user;
        });

        return $this->success($users);
    }

    /**
     * Get the current user.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function show(User $user = null)
    {
        $user = $this->getUser($user);

        $this->authorize('show', $user);

        $user->load('role');

        return $this->success($user);
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
            'role_id' => Role::where('name', $request->role ?: 'User')->first()->id,
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
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function patch(Request $request, User $user = null)
    {
        $user = $this->getUser($user);

        $this->authorize('update', $user);

        $this->validate($request, [
            'name' => 'nullable|max:100',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|max:255|min:6',
            'role' => 'nullable|in:Admin,User',
        ]);

        if (! empty($request->email)) {
            if ($user->email !== $request->email) {
                if (User::where('email', $request->email)->count() > 0) {
                    return $this->error('Email must be unique', [
                        'email' => ['The provided email already exists'],
                    ]);
                }
            }
        }

        $user->fill($request->only([
            'name',
            'email',
        ]));

        if (! empty($request->password)) {
            if (! $request->user()->isAdmin()) {
                return abort(403);
            }
            $user->fill([
                'password' => \Hash::make($request->password),
            ]);
        }

        if (! empty($request->role)) {
            if (! $request->user()->isAdmin()) {
                return abort(403);
            }

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

    /**
     * Update the user.
     *
     * @param \App\User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function update(Request $request, User $user = null)
    {
        $user = $this->getUser($user);

        $this->authorize('update', $user);

        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:255',
        ]);

        if ($user->email !== $request->email) {
            if (User::where('email', $request->email)->count() > 0) {
                return $this->error('Email must be unique', [
                    'email' => ['The provided email already exists'],
                ]);
            }
        }

        $user->fill($request->only(['email', 'name']))->save();

        $user->load('role');

        return $this->created($user);
    }

    /**
     * Update the authenticated user password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function patchPassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6|max:60',
        ]);

        $user = $request->user();

        $credentials = [
            'email' => $user->email,
            'password' => $request->old_password,
        ];

        if (! auth()->attempt($credentials)) {
            return $this->error('Invalid password', [
                'old_password' => ['The provided password does not match the one we have on record'],
            ]);
        }

        $user->fill(['password' => \Hash::make($request->password)])->save();

        return $this->created('Password updated successfully');
    }

    /**
     * Get the given user or the currently authenticated user if null is given.
     *
     * @param \App\User $user The possible user.
     * @return \App\User
     */
    protected function getUser(User $user = null)
    {
        if (! is_null($user)) {
            return $user;
        }

        $user = auth()->user();

        if (! $user) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}
