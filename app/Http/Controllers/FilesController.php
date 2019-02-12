<?php

namespace App\Http\Controllers;

use App\File;
use App\Helpers\FileHelper;
use App\Http\Traits\Responds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    use Responds;

    /**
     * @var string
     */
    protected $disk;

    /**
     * FilesController constructor.
     *
     * @param string $disk
     */
    public function __construct(string $disk = 'files')
    {
        $this->disk = $disk;
    }

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
        $this->authorize('lists', File::class);

        $this->validate($request, [
            'search' => 'nullable|max:255',
            'limit' => 'nullable|int|min:6|max:100',
        ]);

        $files = File::select([
            'id',
            'platform_id',
            'name',
            'app_version',
        ])->with('platform');

        if (! empty($request->search)) {
            $files->whereHas('platform', function ($query) use ($request) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $query->where('name', 'like', "%$request->search%");
            });

            $files->orWhere(function ($query) use ($request) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $query->where('name', 'like', "%$request->search%");
                $query->orWhere('app_version', 'like', "%$request->search%");
            });
        }

        return $this->success($files->paginate($request->limit ?: 10));
    }

    /**
     * Store a newly created file in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->authorize('create', File::class);

        $this->validate($request, [
            'file' => 'required|file|max:10240|mimetypes:text/plain,application/json',
            'app_version' => 'required|max:100',
            'platform_id' => 'required|exists:platforms,id',
        ]);

        // Verify that the platform + app_Version is unique
        $exists = File::where([
            'app_version' => $request->app_version,
            'platform_id' => $request->platform_id,
        ])->exists();

        if ($exists) {
            return $this->error('App version already exists.', [
                'app_version' => ['Version must be unique. Please choose a new version name.'],
            ]);
        }

        // Validate the content of the file
        if (! $this->validateContent($request->file('file'))) {
            return $this->error('Unable to decode file', [
                'file' => ['The file format is incorrect. Please verify the contents of the file.'],
            ]);
        }

        // Save the file
        $path = Storage::disk($this->disk)->putFile('', $request->file('file'));

        $helper = new FileHelper($path);

        // Remove is_current status for the latest file
        $current = File::where('platform_id', $request->platform_id)->current()->first();
        if ($current) {
            $current->fill(['is_current' => false])->save();
        }

        $file = File::create([
            'name' => $helper->name(),
            'path' => $path,
            'app_version' => $request->app_version,
            'platform_id' => $request->platform_id,
            'is_current' => true,
        ]);
        $file->load('platform');

        return $this->success($file->makeHidden('path'));
    }

    /**
     * Display the specified file.
     *
     * @param  \App\File $file
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(File $file)
    {
        $this->authorize('view', $file);

        $file->load('platform');

        return $this->success($file->makeHidden('path'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\File $file
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, File $file)
    {
        $this->authorize('update', $file);

        $this->validate($request, [
            'app_version' => 'nullable|max:100',
            'platform_id' => 'nullable|exists:platforms,id',
        ]);

        if (! empty($request->app_version) && $file->app_version !== $request->app_version) {
            $exists = File::where([
                'app_version' => $request->app_version,
                'platform_id' => empty($request->platform_id) ? $file->platform_id : $request->platform_id,
            ])->exists();

            if ($exists) {
                return $this->error('App version already exists.', [
                    'app_version' => ['Version must be unique. Please choose a new version name.'],
                ]);
            }
        }

        $file->fill($request->only(['app_version', 'platform_id']))->save();

        $file->load('platform');

        return $this->created($file->makeHidden('path'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\File $file
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function delete(File $file)
    {
        $this->authorize('delete', $file);

        if (Storage::disk($this->disk)->exists($file->path)) {
            Storage::disk($this->disk)->delete($file->name);
        }

        $file->delete();

        return $this->created('File deleted successfully');
    }

    /**
     * Download a file.
     *
     * @param \App\File $file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function download(File $file)
    {
        $this->authorize('view', $file);

        $extension = (new FileHelper($file->path))->extension();
        $name = $file->platform->name.'-'.$file->app_version.'.'.$extension;

        $name = strtolower(str_replace(' ', '-', $name));

        return Storage::disk('files')->download($file->name, $name);
    }

    /**
     * Validate the contents of the file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    protected function validateContent($file)
    {
        try {
            $content = $file->get();

            return ! ! json_decode($content);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
