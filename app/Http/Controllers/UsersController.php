<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('lists', User::class);

        return User::with('role')->get();
    }

    public function create(Request $request)
    {
        $this->authorize('create', User::class);
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|max:255|min:6',
        ]);

        return User::create([
            'name' => $request->name,
            'password' => \Hash::make($request->password),
            'email' => $request->email,
            'role' => Role::default()->id,
        ]);
    }
}
