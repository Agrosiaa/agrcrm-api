<?php
namespace App\Http\Controllers\CustomTraits;

use Illuminate\Support\Facades\Log;

trait SendMessageTrait{

    public function sendOrderSms($mobile,$message){
        try{
            // create curl resource
            $ch = curl_init();
            $smsUsername = env('SMS_USERNAME');
            $smsPassword = env('SMS_PASSWORD');
            $message = urlencode($message);
            curl_setopt_array($ch, array(
                CURLOPT_URL => "http://www.smsgateway.center/SMSApi/rest/send?userId=".$smsUsername."&password=".$smsPassword."&senderId=AGRSIA&sendMethod=simpleMsg&msgType=unicode&mobile=".$mobile."&msg=".$message."&duplicateCheck=true&format=json",
                CURLOPT_RETURNTRANSFER => 1
            ));
            // execute
            $output = curl_exec($ch);
            // free
            curl_close($ch);
            return $output;
        }catch (\Exception $e){
            $errorLog = [
                'mobile'=>$mobile,
                'otp'=>$message,
                'action'=>'Send SMS OTP',
                'exception'=>$e->getMessage()
            ];
            Log::critical(json_encode($errorLog));
            return null;
        }
    }

}