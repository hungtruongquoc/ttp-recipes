<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class Controller
{
    /**
     * Return a standardized JSON response for a single resource.
     *
     * @param JsonResource|null $resource
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithResource(?JsonResource $resource, int $status = 200, array $headers = []): JsonResponse
    {
        return $resource
            ? response()->json($resource, $status, $headers)
            : response()->json(null, 204);
    }

    /**
     * Return a standardized JSON response for a collection of resources.
     *
     * @param ResourceCollection $collection
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function respondWithCollection(ResourceCollection $collection, int $status = 200, array $headers = []): JsonResponse
    {
        return response()->json($collection, $status, $headers);
    }
}
