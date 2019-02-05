<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait Responds
{
    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data)
    {
        return JsonResponse::create($data, 200);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function created($data = null)
    {
        return JsonResponse::create($data ?: 'Created', 201);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden($data = null)
    {
        return JsonResponse::create($data ?: 'Forbidden', 403);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function deleted($data = null)
    {
        return JsonResponse::create($data ?: 'Deleted', 201);
    }

    /**
     * @param $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message, $data = [])
    {
        return JsonResponse::create([
            'message' => $message,
            'data' => $data,
        ], 422);
    }
}
