<?php
/**
 * Created by PhpStorm.
 * User: ujjwal
 * Date: 12/6/17
 * Time: 8:17 PM
 */

namespace App\Helpers;


use App\Models\CommentActivity;
use App\Models\PostActivity;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class PushNotificationHelper
{
    static function send($token, $title, $body, $data)
    {
        try {
            $data['title'] = $title;
            $data['body'] = $body;
	        $data['click_action'] = 'FCM_PLUGIN_ACTIVITY';

            if(env('PUSH_NOTIFICATION', 'FCM') == "FCM") {
                Log::info('Push notification - ' . $token, $data);

                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);

                $notificationBuilder = new PayloadNotificationBuilder($title);
                $notificationBuilder->setBody($body)
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData($data);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                if ($token) {
                    //Log::info('Push notification', ['Sending...', $token, $data]);

                    //Log::info('Push notification Marko - ' . $token, $option, $notification, $data);

                   
                    FCM::sendTo($token, $option, $notification, null);

                }
            } else {
                OneSignal::sendNotificationToUser($title,
                    $token,
                    null,
                    $data);

                    Log::info('OneSIGNAL ELSE', [$token, $data, $title]);
            }
        } catch (\Exception $ex) {
            Log::info('Error Marko',[$ex]);

            Log::info('Unable to send Push notification', [$token, $data]);
        }
    }
}
