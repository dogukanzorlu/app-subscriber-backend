<?php

namespace App\Services;

use App\Repositories\DeviceRepository;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Exception;

class DeviceService
{
    /**
     *
     * @var $deviceRepository
     */
    protected $deviceRepository;

    /**
     * DeviceService constructor.
     *
     * @param DeviceRepository $deviceRepository
     */
    public function __construct(DeviceRepository $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * Get all Device.
     *
     * @param $params
     * @return mixed
     */
    public function getAll($params)
    {
        try {
            $data = $this->deviceRepository->getAll($params);
        }catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return $data;
    }

    /**
     * Get Device by id.
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        try {
            $data = $this->deviceRepository->show($id);
        }catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return $data;
    }

    /**
     * Save Device data.
     *
     * @param array $data
     * @return mixed
     */
    public function saveDevice(Array $data)
    {
        $validator = Validator::make($data, [
            'device_uuid' => 'required|string|max:500',
            'app_id' => 'required|string',
            'language' => 'string|nullable',
            'operation_system' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        try {
            $new_device = $this->deviceRepository->save($data);
        }catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return $new_device;
    }

    /**
     * Update Device data.
     *
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function updateDevice(Array $data, $id)
    {
        $validator = Validator::make($data, [
            'app_id' => 'required|string',
            'language' => 'string|nullable',
            'operation_system' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        try {
            $update_device = $this->deviceRepository->update($data, $id);
        }catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return $update_device;
    }

    /**
     * Delete device by id.
     *
     * @param $id
     * @return mixed
     */
    public function deleteById($id)
    {

        try {
            $device = $this->deviceRepository->delete($id);

        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }


        return $device;

    }
}
