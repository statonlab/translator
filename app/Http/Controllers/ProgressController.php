<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responds;
use App\Language;
use App\Services\Translation\TranslationProgress;
use App\User;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    use Responds;

    /**
     * @param \App\Language $language
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function language(Language $language)
    {
        $this->authorize('view', $language);

        $reporter = new TranslationProgress();

        return $this->success($reporter->compute($language));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\User|null $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function user(Request $request, User $user = null)
    {
        if (! is_null($user)) {
            $this->authorize('view', $user);
        } else {
            $user = $request->user();
        }

        $reporter = new TranslationProgress();

        return $this->success($reporter->user($user));
    }
}
