<?php
namespace App\Repositories;

use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SubscriptionRepository
{
    /**
     * @var Builder
     */
    private $subscription;

    /**
     * SubscriptionRepository constructor.
     *
     */
    public function __construct()
    {
        $this->subscription = DB::table('subscriptions');
    }

    /**
     * Get Subscription by Device id
     *
     * @param $id
     * @return mixed
     */
    public function checkSubscription($id)
    {
        try {
            $find_device = DB::table('devices')->where('device_uuid', $id)->first();
            $device_id = $find_device->id;

            $find_subscription = $this->subscription->where('device_id', $device_id)->first();
        }catch (Exception $e){
            throw new InvalidArgumentException($e->getMessage());
        }

        return $find_subscription;
    }

    /**
     * Save Subscription
     *
     * @param $data
     * @return mixed
     */
    public function saveSubscription($data)
    {
        $find_device = DB::table('devices')->where('device_uuid', $data['device_uuid'])->first();

        DB::beginTransaction();

        try {
            if(isset($find_device)){
                $device_id = $find_device->id;

                $find_subscription = $this->subscription->where('device_id', $device_id);
                $get_first = $find_subscription->first();

                if(isset($get_first)){
                    $update = $find_subscription->lockForUpdate()->update([
                        'status' => $data['status'],
                        'expire_at' => $data['expire_at'],
                        'updated_at' => date('m/d/Y h:i:s a', time())
                    ]);

                    $result = $update ? [
                        'device_id' => $device_id,
                        'status' => $data['status'],
                        'expire_at' => $data['expire_at']
                    ] : false;
                }else {
                    $create = $this->subscription->lockForUpdate()->insert([
                        'device_id' => $device_id,
                        'status' => $data['status'],
                        'expire_at' => $data['expire_at'],
                        'created_at' => date('m/d/Y h:i:s a', time()),
                        'updated_at' => date('m/d/Y h:i:s a', time())
                    ]);
                    $result = $create ? [
                        'device_id' => $device_id,
                        'status' => $data['status'],
                        'expire_at' => $data['expire_at']
                    ] : false;
                }
            }else{
                $result = ['Device Not Found'];
            }
        }catch (Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException($e->getMessage());
        }

        DB::commit();

        return $result;
    }
}
