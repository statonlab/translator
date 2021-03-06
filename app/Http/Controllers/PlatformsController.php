<?php

namespace App\Http\Controllers;

use App\Http\Traits\Responds;
use App\Language;
use App\Platform;
use App\Services\Serializers\TranslatedLineDecoder;
use App\Services\Storage\Archive;
use Illuminate\Http\Request;

class PlatformsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request)
    {
        $this->authorize('lists', Platform::class);

        $this->validate($request, [
            'search' => 'nullable|max:255',
            'limit' => 'nullable|int|min:6|max:100',
        ]);

        $platforms = Platform::withCount([
            'languages' => function ($query) {
            },
            'files' => function ($query) {
            },
            'translatedLines as total_lines_count' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('is_current', true);
            },
            'translatedLines as translated_lines_count' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('is_current', true);
                $query->whereNotNull('value');
                $query->where('value', '!=', '');
            },
        ]);

        if (! empty($request->search)) {
            $platforms->whereHas('languages', function ($query) use ($request) {
                $query->where('languages.language', 'like', "%$request->search%");
                $query->orWhere('languages.language_code', 'like', "%$request->search%");
            });
            $platforms->orWhere('name', 'like', "%$request->search%");
        }

        $platforms->orderBy('name', 'asc');
        $data = $platforms->paginate($request->limit ?: 10);
        $data->getCollection()->transform(function ($platform) {
            $platform->progress = $this->computeProgress($platform->translated_lines_count,
                $platform->total_lines_count);

            return $platform;
        });

        return $this->success($data);
    }

    /**
     * Get a list of platforms ids and names only without pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function compressedIndex(Request $request)
    {
        /** @var \App\User $user */
        $user = $request->user();

        if ($user->isAdmin()) {
            $platforms = Platform::select('id', 'name')->orderBy('name', 'asc')->get();
        } else {
            $platforms = $user->platforms()
                ->select(['name', 'platforms.id'])
                ->orderBy('name', 'asc')
                ->get();
        }

        return $this->success($platforms);
    }

    /**
     * @param \App\Platform $platform
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function progress(Platform $platform, Request $request)
    {
        $this->authorize('view', $platform);

        /** @var \App\User $user */
        $user = $request->user();
        if ($user->isAdmin()) {
            $languages = $platform->languages();
        } else {
            $languages = $user->languages()->where('platform_id', $platform->id);
        }

        $languages = $languages->withCount([
            'translatedLines as total_lines' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('is_current', true);
            },
            'translatedLines as completed_lines' => function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->whereNotNull('value');
                $query->where('value', '!=', '');
                $query->where('is_current', 1);
            },
        ])->orderBy('language', 'asc')->get()->map(function ($language) {
            $language->progress = $this->computeProgress($language->completed_lines,
                $language->total_lines);

            return $language;
        });

        return $this->success($languages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
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
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Platform $platform)
    {
        $this->authorize('delete', $platform);

        $platform->delete();

        return $this->created('Platform deleted');
    }

    /**
     * Update a single field.
     *
     * @param \App\Platform $platform
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function patch(Platform $platform, Request $request)
    {
        $this->authorize('update', $platform);

        $this->validate($request, [
            'name' => 'required|max:200|unique:platforms,name',
        ]);

        $platform->fill($request->only('name'))->save();

        $platform->load('languages');

        return $this->created($platform);
    }

    /**
     * List files for a platform.
     *
     * @param \App\Platform $platform
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function files(Platform $platform, Request $request)
    {
        $this->authorize('view', $platform);

        $this->validate($request, [
            'limit' => 'nullable|int|min:6|max:100',
            'search' => 'nullable|max:255',
        ]);

        $files = $platform->files()->orderBy('id', 'desc');

        if (! empty($request->search)) {
            $files->where('app_version', 'like', "%$request->search%");
        }

        return $this->success([
            'platform' => $platform,
            'files' => $files->paginate($request->limit ?: 15),
        ]);
    }

    /**
     * Download files.
     *
     * @param \App\Platform $platform
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function download(Platform $platform, Request $request)
    {
        $this->authorize('update', $platform);

        $this->validate($request, [
            'language_id' => 'nullable|exists:languages,id',
        ]);

        if (! empty($request->language_id)) {
            $language = $platform->languages()->findOrFail($request->language_id);
            $this->authorize('view', $language);

            $languages = collect([$language]);
        } else {
            $languages = $platform->languages;
        }

        // Decode all data
        $dir = uniqid();
        $disk = \Storage::disk('translated');
        $files = collect([]);
        foreach ($languages as $language) {
            $decoder = new TranslatedLineDecoder();
            $lines = $decoder->decode($language);

            $path = "{$dir}/{$language->language_code}.json";
            $files->push($disk->path($path));
            $disk->put($path, json_encode($lines, JSON_PRETTY_PRINT));
        }

        // Create Zip File
        $archive = new Archive($files->toArray(), $disk);
        try {
            $zip = $archive->zip("{$dir}/{$platform->name}.zip");
        } catch (\Exception $exception) {
            return abort(500, $exception->getMessage());
        }

        // Clean up disk
        $files->map(function ($file) use ($disk) {
            $disk->delete($file);
        });

        // Register a file for deletion after 4 hours from now
        // TODO: create TemporaryFile model and job

        return response()->download($zip, basename($zip));
    }

    /**
     * Get progress value.
     *
     * @param $completed
     * @param $total
     * @return float|int
     */
    public function computeProgress($completed, $total)
    {
        return $total == 0 ? 0 : $completed / $total * 100;
    }
}
