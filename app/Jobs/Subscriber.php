<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Subscriber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 9999999;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     */
    public function handle(): bool
    {
        $response = Http::post('http://localhost:8000/api/google/service/subscription', [
            'receipt' => $this->generateRandomString()
        ]);

        try {
            $find_subscription = DB::table('subscriptions')->where('device_id', $this->data->device_id);
            $get_first = $find_subscription->first();

            if($response['status'] == true){
                if(isset($get_first)) {
                    $find_subscription->lockForUpdate()->update([
                        'status' => $response['status'],
                        'expire_at' => $response['expire_at'],
                        'updated_at' => date('m/d/Y h:i:s a', time())
                    ]);
                }
            }
        }catch (Exception $e){
            Log::info($e->getMessage());
            Subscriber::dispatch($this->data)
                ->onConnection('redis')
                ->onQueue('default')
                ->delay(now()->addMinutes(5));
        }
        return true;
    }

    /**
     * Generate random receipt.
     * @param int $length
     * @return string
     */
    protected function generateRandomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString . rand(25, 999);
    }
}
