<?php

namespace App\Services;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Exception;

class GoogleService
{

    /**
     * CheckSubscription.
     *
     * @param array $data
     * @return mixed
     */
    public function checkSubscription(Array $data)
    {
        $validator = Validator::make($data, [
            'receipt' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        try {
            $last_char_mod = intval(substr($data['receipt'], -1)) % 2;
            $last_two_char_mod = intval(substr($data['receipt'], -2)) % 6;

            if ($last_two_char_mod == 0){
                return 'Rate Limit';
            }
            $utc_6 = new DateTime('now', new DateTimeZone('-0600'));
            $utc_6->add(new \DateInterval('PT' . 50 . 'M'));
            $new_expire = $utc_6->format('Y-m-d H:i:s');
            if($last_char_mod != 0){
                $data = ['status' => true, 'expire_at' => $new_expire];
            }else{
                $data = ['status' => false, 'expire_at' => null];
            }
        }catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }

        return $data;
    }

}
