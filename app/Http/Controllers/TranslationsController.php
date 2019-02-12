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
    use Responds;

    /**
     * Get a list of languages that the current user is responsible for.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function languages(Request $request)
    {
        /** @var \App\User $user */
        $user = $request->user();

        if ($user->isAdmin()) {
            $languages = Language::get();
        } else {
            $languages = $user->languages;
        }

        return $this->success($languages->map(function ($language) {
            return [
                'label' => $language->language,
                'value' => $language->id,
            ];
        }));
    }

    public function lines(Platform $platform, Request $request)
    {
        $this->validate($request, [
            'new' => 'nullable|boolean',
            'needs_updating' => 'nullable|boolean',
            'language_id' => 'nullable|exists:languages,id',
        ]);

        $file = $platform->files()->current()->first();

        if (! $file) {
            return $this->error('No primary translation files have been uploaded for this platform.',
                [], 422);
        }

        $lines = TranslatedLine::with(['serializedLine', 'language'])
            ->where('file_id', $file->id);

        $lines = $this->filterLines($request, $lines)->get();

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
        if ($request->new && $request->needs_updating) {
            $lines = $lines->where(function ($query) {
                /** @var TranslatedLine $query */
                $query->needsTranslation();
            })->orWhere(function ($query) {
                /** @var TranslatedLine $query */
                $query->needsUpdating();
            });
        } elseif ($request->new) {
            $lines = $lines->needsTranslation();
        } elseif ($request->needs_updating) {
            $lines = $lines->needsUpdating();
        }

        if ($request->language_id) {
            $is_assigned = $request->user()
                ->languages()
                ->where('languages.id', $request->language_id)
                ->exists();
            if ($is_assigned) {
                $lines = $lines->where('language_id', $request->language_id);
            }
        }

        return $lines;
    }
}
