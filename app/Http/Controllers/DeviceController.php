<?php

namespace App\Http\Controllers;

use App\Services\DeviceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     *
     * @var $deviceService
     */
    protected $deviceService;

    /**
     * DeviceController Constructor
     *
     * @param DeviceService $deviceService
     *
     */
    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $result = $this->deviceService->getAll($_GET);
            $meta = [
                'status' => 200,
                'page' => $result['meta']['page'],
                'count' => $result['meta']['count'],
            ];
        } catch (Exception $e) {
            $meta = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
            $result['data'] = null;
        }

        return $this->respond_with_data($meta, $result['data']);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {

        try {
            $data = $this->deviceService->getById($id);
            $meta = [
                'status' => $data['device'] == null ? 404 : 200
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

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        $data = $request->only([
            'device_uuid',
            'app_id',
            'language',
            'operation_system',
        ]);

        try {
            $meta = [
                'status' => 200,
            ];
            $data = $this->deviceService->saveDevice($data);
        } catch (Exception $e) {
            $meta = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
            $data = null;
        }

        return $this->respond_with_data($meta, $data);

    }

    /**
     *  Update resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        $data = $request->only([
            'app_id',
            'language',
            'operation_system',
        ]);

        try {
            $data = $this->deviceService->updateDevice($data, $id);
            $meta = [
                'status' => $data == null ? 404 : 200,
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

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     */
    public function destroy($id): JsonResponse
    {
        try {
            $data = $this->deviceService->deleteById($id);
            $meta = [
                'status' => $data == null ? 404 : 204,
            ];
        } catch (Exception $e) {
            $meta = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
            $data = null;
        }

        return $this->respond_with_data($meta, null);
    }
}
