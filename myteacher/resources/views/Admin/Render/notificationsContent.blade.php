@if(count(\Illuminate\Support\Facades\Auth::guard('student')->user()->notifications) > 0)
    @foreach(\Illuminate\Support\Facades\Auth::guard('student')->user()->notifications as $notification)
        <ul class="list-group list-group-flush">
            <li class="list-group-item list-group-item-action dropdown-notifications-item notificationItem"
                id="notifications">
                <div class="d-flex">
                    @php
                        $type = basename(str_replace("\\",'/',$notification->type))
                    @endphp
                    <a href="{{$type=="NewMaterialPDFNotifications" ? route('students_material-pdfs.index'): route('students_material-videos.index')}}">
                        <div class="flex-grow-1">
                            <p class="mb-0">{{$notification->data['message']}}</p>
                            <small
                                    class="text-muted">{{$notification->created_at->diffForHumans()}}</small>
                        </div>
                    </a>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        @if($notification->unread())
                            <a href="javascript:void(0)" id="iconRead"
                               class="dropdown-notifications-read"><span
                                        class="badge badge-dot"></span></a>
                        @endif
                        <a href="javascript:void(0)" id="iconRead"
                           class="dropdown-notifications-archive"><span
                                    class="ti ti-x"></span></a>
                    </div>
                </div>

            </li>
        </ul>
    @endforeach
@else
    <ul class="list-group list-group-flush">
        <li class="list-group-item list-group-item-action dropdown-notifications-item">
            <div class="d-flex">
                <h5>لا يوجد اشعارات اليوم</h5>
            </div>
        </li>
    </ul>
@endif
