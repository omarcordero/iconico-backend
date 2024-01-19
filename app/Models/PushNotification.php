<?php
/**
 * Created by PhpStorm.
 * User: ujjwal
 * Date: 7/23/18
 * Time: 12:57 AM
 */

namespace App\Models;


use App\Helpers\PushNotificationHelper;

class PushNotification
{
    private $token;
    private $title;
    private $body;
    private $data;

    /**
     * PushNotification constructor.
     */
    public function __construct($token, $title, $body, $data)
    {
        $this->token = $token;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function send() {
        PushNotificationHelper::send($this->token, $this->title, $this->body, $this->data);
    }
}