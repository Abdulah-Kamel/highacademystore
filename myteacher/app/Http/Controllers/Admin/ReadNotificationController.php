<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReadNotificationController extends Controller
{
    public function read_notifications(): \Illuminate\Http\JsonResponse
    {
        Auth::guard('student')->user()->notifications->markAsRead();
        $notificationsContentHtml = view('Admin.Render.notificationsContent')->render();
        $notificationsIconHtml = view('Admin.Render.notificationsIcon')->render();
        return response()->json(['notificationsContentHtml' => $notificationsContentHtml, 'notificationsIconHtml' => $notificationsIconHtml]);
    }

    public function increase_notification_icon(): \Illuminate\Http\JsonResponse
    {
            $notificationsIconHtml = view('Admin.Render.notificationsIcon')->render();
            return response()->json(['notificationsIconHtml' => $notificationsIconHtml]);
    }

}
