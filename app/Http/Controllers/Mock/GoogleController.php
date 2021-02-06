<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use App\Services\GoogleService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoogleController extends Controller{

    /**
     *
     * @var $googleService
     */
    protected $googleService;

    /**
     * DeviceController Constructor
     *
     * @param GoogleService $googleService
     *
     */
    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }


    public function post(Request $request): JsonResponse
    {
        $data = $request->only([
            'receipt'
        ]);

        try {
            $response = $this->googleService->checkSubscription($data);
        } catch (Exception $e) {
            $response = $e->getMessage();
        }

        if($response == 'Rate Limit'){
            return response()->json($response, 500);
        }else{
            return response()->json($response, 200);
        }
    }
}

