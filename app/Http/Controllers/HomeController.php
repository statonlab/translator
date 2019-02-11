<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param string $any Nullable parameters that vue can handle
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($any = null)
    {
        $user = auth()->user();

        $user->load('role');

        return view('home')->with([
            'user' => $user,
        ]);
    }
}
