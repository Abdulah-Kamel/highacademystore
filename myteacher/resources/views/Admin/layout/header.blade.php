<nav
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">


    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0   d-xl-none ">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>


    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">


        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0"
                   href="javascript:void(0);">
                    <i class="ti ti-search ti-md me-2"></i>
                    <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                </a>
            </div>
        </div>
        <!-- /Search -->


        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <!-- Language -->
            {{--            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">--}}
            {{--                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"--}}
            {{--                   data-bs-toggle="dropdown">--}}
            {{--                    <i class='ti ti-language rounded-circle ti-md'></i>--}}
            {{--                </a>--}}
            {{--                <ul class="dropdown-menu dropdown-menu-end">--}}
            {{--                    <li>--}}
            {{--                        <a class="dropdown-item" href="javascript:void(0);" data-language="en"--}}
            {{--                           data-text-direction="ltr">--}}
            {{--                            <span class="align-middle">English</span>--}}
            {{--                        </a>--}}
            {{--                    </li>--}}
            {{--                    <li>--}}
            {{--                        <a class="dropdown-item" href="javascript:void(0);" data-language="fr"--}}
            {{--                           data-text-direction="ltr">--}}
            {{--                            <span class="align-middle">French</span>--}}
            {{--                        </a>--}}
            {{--                    </li>--}}
            {{--                    <li>--}}
            {{--                        <a class="dropdown-item" href="javascript:void(0);" data-language="ar"--}}
            {{--                           data-text-direction="rtl">--}}
            {{--                            <span class="align-middle">Arabic</span>--}}
            {{--                        </a>--}}
            {{--                    </li>--}}
            {{--                    <li>--}}
            {{--                        <a class="dropdown-item" href="javascript:void(0);" data-language="de"--}}
            {{--                           data-text-direction="ltr">--}}
            {{--                            <span class="align-middle">German</span>--}}
            {{--                        </a>--}}
            {{--                    </li>--}}
            {{--                </ul>--}}
            {{--            </li>--}}
            <!--/ Language -->


            <!-- Style Switcher -->
            <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                   data-bs-toggle="dropdown">
                    <i class='ti ti-md'></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                            <span class="align-middle"><i class='ti ti-sun me-2'></i>Light</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                            <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                        <span class="align-middle"><i
                                                class="ti ti-device-desktop me-2"></i>System</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- / Style Switcher-->
            @if(\Illuminate\Support\Facades\Auth::guard('student')->check())
                <!-- Notification -->
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                    <a class="nav-link dropdown-toggle hide-arrow notificationIcon notificationIconContent" href=""
                       data-bs-toggle="dropdown"
                       data-bs-auto-close="outside" aria-expanded="false">
                        <i class="ti ti-bell ti-md"></i>
                        @if(count(\Illuminate\Support\Facades\Auth::guard('student')->user()->unreadnotifications) > 0)
                            <span class="badge bg-danger rounded-pill badge-notifications">{{count(\Illuminate\Support\Facades\Auth::guard('student')->user()->unreadnotifications)}}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0">
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto">الاشعارات</h5>
                                <a href="" class="dropdown-notifications-all text-body"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="Remove all"><i
                                        class="ti ti-basket fs-4"></i></a>
                            </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container notificationsContent">
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
                        </li>
                        <li class="dropdown-menu-footer border-top">
                            <a href=""
                               class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                               مشاهدة كل الاشعارات
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ Notification -->
            @endif

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                   data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        @if(\Illuminate\Support\Facades\Auth::guard('admin')->check())
                            <img src="{{\Illuminate\Support\Facades\Auth::guard('admin')->user()->image}}" alt
                                 class="h-auto rounded-circle">
                        @elseif(\Illuminate\Support\Facades\Auth::guard('student')->check())
                            <img src="{{\Illuminate\Support\Facades\Auth::guard('student')->user()->image}}" alt
                                 class="h-auto rounded-circle">
                        @endif

                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        @if(\Illuminate\Support\Facades\Auth::guard('admin')->check())
                                            <img
                                                src="{{\Illuminate\Support\Facades\Auth::guard('admin')->user()->image}}"
                                                alt
                                                class="h-auto rounded-circle">
                                        @elseif(\Illuminate\Support\Facades\Auth::guard('student')->check())
                                            <img
                                                src="{{\Illuminate\Support\Facades\Auth::guard('student')->user()->image}}"
                                                alt
                                                class="h-auto rounded-circle">
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    @if(\Illuminate\Support\Facades\Auth::guard('admin')->check())
                                        <span
                                            class="fw-medium d-block">{{\Illuminate\Support\Facades\Auth::guard('admin')->user()->name}}</span>
                                    @elseif(\Illuminate\Support\Facades\Auth::guard('student')->check())
                                        <span
                                            class="fw-medium d-block">{{\Illuminate\Support\Facades\Auth::guard('student')->user()->name}}</span>
                                    @endif

                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{route('logout')}}">
                            <i class="ti ti-logout me-2 ti-sm"></i>
                            <span class="align-middle">تسجيل الخروج</span>
                        </a>
                    </li>
                </ul>

            </li>
            <!--/ User -->


        </ul>
    </div>


    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper  d-none">
        <input type="text" class="form-control search-input container-xxl border-0" placeholder="Search..."
               aria-label="Search...">
        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
    </div>


</nav>
