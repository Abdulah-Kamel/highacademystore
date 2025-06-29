@extends('Admin.layout.index')
@section('title')
    الحصص اليومية
@endsection
@section('content')
    <div class="container text-center">
        <div class="row">
            <div class="col-12">
                <img src="{{asset('Admin')}}/teacher.webp" alt="" width="250px" height="250px">
            </div>
            @forelse($sessions as $session)

                <div class="col-12 mt-3">
                    <h4>{{$session->name}} - {{$session->grades->name}}</h4>
                    <div class="d-flex justify-content-evenly">
                        <p>تبدء في الساعة</p>
                        <p>تنتهي في الساعة</p>
                    </div>
                    <div class="d-flex justify-content-evenly">
                        <p style="font-weight: bold; color: green">{{ \Carbon\Carbon::parse($session->start_from)->format('H:i') }}</p>
                        <p style="font-weight: bold; color: red">{{ \Carbon\Carbon::parse($session->start_to)->format('H:i') }}</p>
                    </div>
                    @if( now()->addHours(3)->format('H:i:s') >= $session->start_from && now()->addHours(3)->format('H:i:s') <= $session->start_to )
                        <h4>
                            <span style="font-weight: bold; color: red">(جارية الان)</span>
                        </h4>
                        <a href="{{route('attendances.show',$session->id)}}" class="btn btn-success">تسجيل الحضور</a>
                        <a href="{{route('money.show',$session->id)}}" class="btn btn-danger"> دفع فلوس الشهر</a>
                    @elseif(now()->addHours(3)->format('H:i:s') >= $session->start_to)
                        <h4><span style="font-weight: bold; color: green">(انتهت)</span></h4>
                        <a href="{{route('money.show',$session->id)}}" class="btn btn-danger">دفع فلوس الشهر</a>
                    @elseif(now()->addHours(3)->format('H:i:s') < $session->start_from)
                        <h4><span style="font-weight: bold; color: orange">(لم تبدء حتي الان)</span></h4>
                        <a href="{{route('money.show',$session->id)}}" class="btn btn-danger">دفع فلوس الشهر</a>
                    @endif
                </div>
            @empty
                <div class="text-center">
                    <h4>لا يوجد حصص اليوم</h4>
                </div>
            @endforelse
        </div>
    </div>
@endsection

