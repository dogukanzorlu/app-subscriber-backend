<?php

namespace App\Services;

use App\Repositories\SubscriptionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Exception;

class SubscriptionService
{
    /**
     *
     * @var $subscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * SubscriptionService constructor.
     *
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Get Subscription by token.
     *
     * @param array $data
     * @return mixed
     */
    public function checkSubscription(Array $data)
    {
        $validator = Validator::make($data, [
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        try {
            $token_decode = base64_decode($data['token']);

            $subscriber = $this->subscriptionRepository->checkSubscription($token_decode);
        }catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return $subscriber;
    }

    /**
     * Save Subscription data.
     *
     * @param array $data
     * @return mixed
     */
    public function saveSubscription(Array $data)
    {
        $validator = Validator::make($data, [
            'token' => 'required|string',
            'receipt' => 'required|string'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        try {
            $token_decode = base64_decode($data['token']);

            $request = Request::create('/api/google/service/subscription', 'POST', ['receipt' => $data['receipt']]);
            $response = Route::dispatch($request);
            $content = json_decode($response->getContent(), true);

            if(!isset($content['status'])){
                throw new InvalidArgumentException("Rate Limit");
            }

            if($content['status'] == true){
                $data = ['device_uuid' => $token_decode,
                    'status' => $content['status'],
                    'expire_at' => $content['expire_at']];
                $new_device = $this->subscriptionRepository->saveSubscription($data);
            }else{
                $new_device = $content;
            }
        }catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return $new_device;
    }
}
