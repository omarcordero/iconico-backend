<?php

namespace App\Notifications\Admin;

use App\Http\Controllers\Admin\UserController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class NewUser extends Notification implements ShouldBroadcast
{
    use Queueable;

    private $user;

    private $role;

    /**
     * Create a new notification instance.
     *
     * @param $user
     * @param $role
     */
    public function __construct($user, $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return explode(',', config('admin-notification.channels'));
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return env('ADMIN_TO_EMAIL_ADDRESS');
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = action('Admin\UserController@show', ['user' => $this->user->id]);

        return (new MailMessage)
                    ->greeting('Hello!')
                    ->line('Nuevo usuario registrado')
                    ->action('Ver usuario', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'role' => $this->role
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'user_id' => $this->user->id,
            'role' => $this->role
        ]);
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.Auth.User.User.'.$this->user->id;
    }
}
