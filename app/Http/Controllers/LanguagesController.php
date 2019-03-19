<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responds;
use App\Language;
use App\Platform;
use App\User;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    /**
     * List languages.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('lists', Language::class);

        $this->validate($request, [
            'search' => 'nullable|max:255',
        ]);

        $languages = Language::with(['users', 'platform']);

        if ($request->search) {
            $languages->whereHas('users', function ($query) use ($request) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $query->where('users.name', 'like', "%{$request->search}%");
            });

            $languages->orWhereHas('platform', function ($query) use ($request) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $query->where('platforms.name', 'like', "%{$request->search}%");
            });

            $languages->orWhere(function ($query) use ($request) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $query->where('language', 'like', "%{$request->search}%");
                $query->orWhere('language_code', 'like', "%{$request->search}%");
            });
        }

        $languages = $languages->orderBy('languages.language', 'asc')->paginate(20);

        return $this->success($languages);
    }

    /**
     * Create new language.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->authorize('create', Language::class);

        $this->validate($request, [
            'language' => 'required|max:100',
            'language_code' => 'required|max:10',
            'image' => 'nullable|max:255',
            'platform_id' => 'required|int|exists:platforms,id',
        ]);

        $platform = Platform::find($request->platform_id);
        $this->authorize('update', $platform);

        // check if unique constraints are met
        $exists = Language::where([
            'platform_id' => $request->platform_id,
            'language_code' => $request->language_code,
        ])->exists();

        if ($exists) {
            return $this->error('Duplicate entry', [
                'language_code' => ['the language code already exists for this platform.'],
            ]);
        }

        $language = Language::create($request->only([
            'language',
            'language_code',
            'image',
            'platform_id',
        ]));

        return $this->success($language);
    }

    /**
     * Delete a language.
     *
     * @param \App\Language $language
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(Language $language)
    {
        $this->authorize('delete', $language);

        $language->delete();

        return $this->created();
    }

    /**
     * @param \App\Language $language
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function patch(Language $language, Request $request)
    {
        $this->authorize('update', $language);

        $this->validate($request, [
            'language' => 'nullable|max:100',
            'language_code' => 'nullable|max:10',
            'image' => 'nullable|max:255',
            'platform_id' => 'nullable|int|exists:platforms,id',
        ]);

        $language->fill($request->only([
            'language',
            'language_code',
            'image',
            'platform_id',
        ]))->save();

        $language->load('users');

        return $this->created($language);
    }

    /**
     * Toggle an assignment.
     *
     * @param \App\Language $language
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function toggleAssignment(Language $language, Request $request)
    {
        $this->authorize('assign', $language);

        $this->validate($request, [
            'user_id' => 'required|int|exists:users,id',
        ]);

        $changes = $language->users()->toggle([$request->user_id]);

        $results = [
            'attached' => count($changes['attached']) > 0,
            'detached' => count($changes['detached']) > 0,
        ];

        $user = User::find($request->user_id);

        $platforms = $user->languages->map(function ($language) {
            return $language->platform_id;
        })->unique();
        $user->platforms()->sync($platforms);

        return $this->created($results);
    }

    /**
     * Get a list of users assigned to a language.
     *
     * @param \App\Language $language
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function users(Language $language)
    {
        $this->authorize('assign', $language);

        return $this->success($language->users->only(['id', 'name']));
    }
}
