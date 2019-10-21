<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responds;
use App\Language;
use App\Platform;
use App\TranslatedLine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TranslationsController extends Controller
{
    /**
     * Get all assigned platforms.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignedPlatforms(Request $request)
    {
        /** @var \App\User $user */
        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->success(Platform::orderBy('name', 'asc')->get());
        }

        return $this->success($user->platforms);
    }

    /**
     * Get a list of languages that the current user is responsible for.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function languages(Platform $platform, Request $request)
    {
        /** @var \App\User $user */
        $user = $request->user();

        if ($user->isAdmin()) {
            $languages = $platform->languages()
                ->orderBy('language', 'asc')
                ->orderBy('language_code', 'asc')
                ->get();
        } else {
            $languages = $platform->languages()
                ->whereHas('users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->orderBy('language', 'asc')
                ->orderBy('language_code', 'asc')
                ->get();
        }

        return $this->success($languages);
    }

    /**
     * @param \App\Language $language
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function lines(Language $language, Request $request)
    {
        $this->authorize('view', $language);

        $this->validate($request, [
            'new_only' => 'nullable|boolean',
            'needs_updating' => 'nullable|boolean',
            'language_id' => 'nullable|exists:languages,id',
            'search' => 'nullable|max:255',
        ]);

        /** @var \App\User $user */
        $user = $request->user();

        $platform = $language->platform;
        $file = $platform->files()->current()->first();

        if (! $file) {
            $message = 'No primary translation files have been uploaded for this platform.';

            return $this->error($message, [], 422);
        }

        $lines = TranslatedLine::current()->with([
            'serializedLine',
            'language',
            'user' => function ($query) {
                $query->select(['users.id', 'users.name']);
            },
        ])->where('language_id', $language->id);

        if (! $user->isAdmin()) {
            $lines->whereIn('language_id', $user->languages);
        }

        // Change the limit based on filters. If we have filters, we don't want to
        // paginate. Since every time the user translates a line, the filters produce
        // different results

        $lines = $this->filterLines($request, $lines)
            ->orderBy('value', 'asc')
            ->orderBy('id', 'desc');

        if ($request->needs_updating || $request->new_only) {
            $lines = $lines->paginate($lines->count());
        } else {
            $lines = $lines->paginate(25);
        }

        return $this->success($lines);
    }

    /**
     * Apply filters to lines
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\TranslatedLine $lines
     * @return \App\TranslatedLine|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function filterLines(Request $request, Builder $lines)
    {
        $lines->where(function (Builder $query) use ($request) {
            if ($request->new_only && $request->needs_updating) {
                $query->where(function (Builder $query) {
                    $query->where(function (Builder $query) {
                        /** @var TranslatedLine $query */
                        $query->needsTranslation();
                    })->orWhere(function (Builder $query) {
                        /** @var TranslatedLine $query */
                        $query->needsUpdating();
                    });
                });
            } elseif ($request->new_only) {
                /** @var TranslatedLine $query */
                $query->needsTranslation();
            } elseif ($request->needs_updating) {
                /** @var TranslatedLine $query */
                $query->needsUpdating();
            }

            if ($request->language_id) {
                $is_assigned = $request->user()
                    ->languages()
                    ->where('languages.id', $request->language_id)
                    ->exists();
                if ($is_assigned) {
                    $query->where('language_id', $request->language_id);
                }
            }

            if (! empty($request->search)) {
                $term = $request->search;

                $query->where(function (Builder $query) use ($term) {
                    $query->where('translated_lines.value', 'like', "%$term%");
                    $query->orWhereHas('serializedLine',
                        function (Builder $query) use ($term) {
                            $query->where('serialized_lines.value', 'like', "%$term%");
                        });
                });
            }
        });

        return $lines;
    }

    /**
     * Perform the translation of a line.
     *
     * @param \App\TranslatedLine $translated_line
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function translate(TranslatedLine $translated_line, Request $request)
    {
        $this->authorize('update', $translated_line);

        $this->validate($request, [
            'value' => 'required|max:255',
        ]);

        $translated_line->fill([
            'user_id' => $request->user()->id,
            'value' => $request->value,
            'needs_updating' => false,
        ])->save();

        $translated_line->load([
            'serializedLine',
            'language',
            'user' => function ($query) {
                $query->select(['users.id', 'users.name']);
            },
        ]);

        return $this->created($translated_line);
    }
}
