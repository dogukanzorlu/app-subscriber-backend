<?php
namespace App\Repositories;

use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DeviceRepository
{
    /**
     * @var Builder
     */
    private $device;

    /**
     * CommentRepository constructor.
     *
     */
    public function __construct()
    {
        $this->device = DB::table('devices');
    }

    /**
     * Get All Device
     *
     * @param $params
     * @return mixed
     */
    public function getAll($params): array
    {
        try {
            $page = isset($params['page']) ? $params['page'] : 0;
            $limit = isset($params['limit']) ? $params['limit'] : 10000;

            $data =  $this->device->skip($page * $limit)->take($limit)->get();
            $meta = ['page' => $page, 'count' => $data->count()];
        }catch (Exception $e){
            throw new InvalidArgumentException($e->getMessage());
        }

        return ['meta' => $meta, 'data' => $data];
    }

    /**
     * Get Device by device uid
     *
     * @param $id
     * @return mixed
     */
    public function show($id): array
    {
        try {
            $device = $this->device
                ->where('device_uuid', '=', $id);
            $pure_data = $device->first();
            $subscription = DB::table('devices')
                ->join('subscriptions', 'devices.id', '=', 'subscriptions.device_id')
                ->select('subscriptions.*')
                ->first();

            return ['device' => $pure_data, 'subscription' => $subscription];
        }catch (Exception $e){
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Save Device
     *
     * @param $data
     * @return mixed
     */
    public function save($data): array
    {
        $find_or_create = $this->device->where('device_uuid', $data['device_uuid'])->first();

        DB::beginTransaction();

        try {
            if(isset($find_or_create)){
                $device_uuid = $find_or_create->device_uuid;
            }else{
                $create = $this->device->lockForUpdate()->insert([
                    'device_uuid' => $data['device_uuid'],
                    'app_id' => $data['app_id'],
                    'language' => $data['language'],
                    'operation_system' => $data['operation_system'],
                    'created_at' => date('m/d/Y h:i:s a', time()),
                    'updated_at' => date('m/d/Y h:i:s a', time())
                ]);
                $device_uuid = $create ? $data['device_uuid'] : false;
            }
        }catch (Exception $e){
            DB::rollBack();

            throw new InvalidArgumentException($e->getMessage());
        }

        DB::commit();

        return ["token" => base64_encode($device_uuid)];
    }

    /**
     * Update Device
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($data, $id)
    {
        $device = $this->device->where('device_uuid', $id);

        DB::beginTransaction();

        try {
            $device->lockForUpdate()->update([
                'app_id' => $data['app_id'],
                'language' => $data['language'],
                'operation_system' => $data['operation_system'],
                'updated_at' => date('m/d/Y h:i:s a', time())
            ]);
        }catch (Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException($e->getMessage());
        }

        DB::commit();

        return $device->first();
    }


    /**
     * Destroy Device
     *
     * @param $id
     * @return mixed
     */
    public function delete($id): ?int
    {
        $device = $this->device->where('device_uuid', $id);

        DB::beginTransaction();

        try {
            if($device->first() != null){
                $device->lockForUpdate()->delete();
                $device = 204;
            }else{
                $device = null;
            }
        }catch (Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException($e->getMessage());
        }

        DB::commit();

        return $device;
    }
}
