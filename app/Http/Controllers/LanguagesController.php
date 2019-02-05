<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responds;
use App\Language;
use App\User;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    use Responds;

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

        $languages = Language::with('users');

        if ($request->search) {
            $languages->whereHas('users', function ($query) use ($request) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $query->where('users.name', 'like', "%{$request->search}%");
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
        ]);

        return $this->success(Language::create($request->only([
            'language',
            'language_code',
            'image',
        ])));
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
        ]);

        $language->fill($request->only(['language', 'language_code', 'image']))->save();

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

        return $this->created([
            'attached' => count($changes['attached']) > 0,
            'detached' => count($changes['detached']) > 0,
        ]);
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
