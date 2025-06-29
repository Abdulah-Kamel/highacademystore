<i class="ti ti-bell ti-md"></i>
@if(count(\Illuminate\Support\Facades\Auth::guard('student')->user()->unreadnotifications) > 0)
    <span class="badge bg-danger rounded-pill badge-notifications"
          id="notificationIconCounter">{{count(\Illuminate\Support\Facades\Auth::guard('student')->user()->unreadnotifications)}}</span>
@endif
