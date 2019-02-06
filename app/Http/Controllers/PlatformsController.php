<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responds;
use App\Platform;
use Illuminate\Http\Request;

class PlatformsController extends Controller
{
    use Responds;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('lists', Platform::class);

        $this->validate($request, [
            'search' => 'nullable|max:255',
            'limit' => 'nullable|int|min:6|max:100',
        ]);

        $platforms = Platform::withCount('languages');

        if (! empty($request->search)) {
            $platforms->whereHas('languages', function ($query) use ($request) {
                $query->where('languages.language', 'like', "%$request->search%");
                $query->orWhere('languages.language_code', 'like', "%$request->search%");
            });
            $platforms->orWhere('name', 'like', "%$request->search%");
        }

        return $this->success($platforms->paginate($request->limit ?: 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->authorize('create', Platform::class);

        $this->validate($request, [
            'name' => 'required|max:200|unique:platforms,name',
        ]);

        $platform = Platform::create([
            'name' => $request->name,
        ]);

        $platform->load('languages');

        return $this->created($platform);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Platform $platform
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Platform $platform)
    {
        $this->authorize('view', $platform);

        $platform->load('languages');

        return $this->success($platform);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Platform $platform
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Platform $platform)
    {
        $this->authorize('update', $platform);

        $this->validate($request, [
            'name' => 'required|max:200|unique:platforms,name',
        ]);

        $platform->fill($request->only(['name']))->save();

        $platform->load('languages');

        return $this->created($platform);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Platform $platform
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Platform $platform)
    {
        $this->authorize('delete', $platform);

        $platform->delete();

        return $this->created('Platform deleted');
    }
}
