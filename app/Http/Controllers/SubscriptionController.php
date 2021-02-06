<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     *
     * @var $subscriptionService
     */
    protected $subscriptionService;

    /**
     * SubscriptionController Constructor
     *
     * @param SubscriptionService $subscriptionService
     *
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkSubscription(Request $request): JsonResponse
    {
        $data = $request->only([
            'token'
        ]);

        try {
            $data = $this->subscriptionService->checkSubscription($data);
            $meta = [
                'status' => $data == null ? 404 : 200
            ];
        } catch (Exception $e) {
            $meta = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return $this->respond_with_data($meta, $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveSubscription(Request $request): JsonResponse
    {

        $data = $request->only([
            'token',
            'receipt'
        ]);

        try {
            $data = $this->subscriptionService->saveSubscription($data);
            $meta = [
                'status' => $data == ['Device Not Found'] ? 404 : 200,
            ];
        } catch (Exception $e) {
            $meta = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
            $data = null;
        }

        return $this->respond_with_data($meta, $data);

    }
}
