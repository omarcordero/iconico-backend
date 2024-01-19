<?php

namespace App\Http\Controllers\Admin\Json;

use App\Events\Auth\Registered;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Validator;

class NotificationController extends Controller
{
    /**
     * Unread notifications
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function readNotifications(Request $request)
    {
        Auth::user()->unreadNotifications->where('type', $request->type)->markAsRead();
        return response()->json(["success" => 1]);
    }

    /**
     * Delete notifications
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteNotifications(Request $request)
    {
        Auth::user()->notifications()->where('type', $request->type)->delete();
        return response()->json(["success" => 1]);
    }
}
