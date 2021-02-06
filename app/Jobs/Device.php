<?php

namespace App\Jobs;

use DateTime;
use DateTimeZone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Device implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 9999999;

    /**
     * Create a new job instance.
     *
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle(): bool
    {
        $utc_6 = new DateTime('now', new DateTimeZone('-0600'));
        $utc_6->add(new \DateInterval('PT' . 10 . 'M'));
        $new_expire = $utc_6->format('Y-m-d H:i:s');

        $expired_subscribers = DB::table('devices')
            ->where('operation_system', '=', 'Android')
            ->join('subscriptions', 'devices.id', '=', 'subscriptions.device_id')
            ->select('subscriptions.*')
            ->where('expire_at', '<', $new_expire)
            ->get();

        foreach ($expired_subscribers as $data){
            Subscriber::dispatch($data)->onConnection('redis')->onQueue('default');
        }

        return true;
    }
}
