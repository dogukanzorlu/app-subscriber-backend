<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Render with meta data as JSON.
     *
     * @return JsonResponse
     * @param $meta
     * @param $data
     */
    protected function respond_with_data(array $meta, $data): JsonResponse
    {
        return response()->json(['meta' => $meta, 'data'=>$data], $meta['status']);
    }
}
