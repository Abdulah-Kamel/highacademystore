@extends('admin.layouts.master')

@section('content')
<div class="page-toolbar px-xl-4 px-sm-2 px-0 py-3">
    <div class="container-fluid">
        <div class="row g-3 mb-3 align-items-center">
            <div class="col">
                <ol class="breadcrumb bg-transparent mb-0">
                    <li class="breadcrumb-item"><a class="text-secondary" href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-secondary" href="#">Account</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Settings</li>
                </ol>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-auto">
                <h1 class="fs-5 color-900 mt-1 mb-0">Welcome back, Allie!</h1>
                <small class="text-muted">You have 12 new messages and 7 new notifications.</small>
            </div>
            <div class="col d-flex justify-content-lg-end mt-2 mt-md-0">
                <div class="p-2 me-md-3">
                    <div><span class="h6 mb-0">8.18K</span> <small class="text-secondary"><i class="fa fa-angle-up"></i>
                            1.3%</small></div>
                    <small class="text-muted text-uppercase">Income</small>
                </div>
                <div class="p-2 me-md-3">
                    <div><span class="h6 mb-0">1.11K</span> <small class="text-secondary"><i class="fa fa-angle-up"></i>
                            4.1%</small></div>
                    <small class="text-muted text-uppercase">Expense</small>
                </div>
                <div class="p-2 pe-lg-0">
                    <div><span class="h6 mb-0">3.66K</span> <small class="text-danger"><i class="fa fa-angle-down"></i>
                            7.5%</small></div>
                    <small class="text-muted text-uppercase">Revenue</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body px-xl-4 px-sm-2 px-0 py-lg-2 py-1 mt-0 mt-lg-3">
    <div class="container-fluid">
        <div class="row g-3">
            <div class="col-xxl-3 col-lg-4 col-md-4">
                <div class="list-group list-group-custom sticky-top me-xl-4" style="top: 100px;">
                    <a class="list-group-item list-group-item-action" href="#list-item-1">Profile Details</a>
                    <a class="list-group-item list-group-item-action" href="#list-item-2">Change Password</a>
                    {{-- <a class="list-group-item list-group-item-action" href="#list-item-3">Notifications
                        Settings</a>
                    <a class="list-group-item list-group-item-action" href="#list-item-4">Social Profiles</a> --}}
                </div>
            </div>
            <div class="col-xxl-8 col-lg-8 col-md-8">
                <div id="list-item-1" class="card fieldset border border-muted mt-0">

                    <span class="fieldset-tile text-muted bg-body">Profile Details:</span>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('dashboard.update.account') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <label class="col-md-3 col-sm-4 col-form-label">Avatar</label>
                                    <div class="col-md-9 col-sm-8">
                                        <div class="image-input avatar xxl rounded-4"
                                            style="background-image: url(admin/assets/img/avatar.png)">
                                            <div class="avatar-wrapper rounded-4"
                                                style="background-image: url({{ asset('/storage/images/admins/'.auth()->user()->photo) }})">
                                            </div>
                                            <div class="file-input">
                                                <input type="file" class="form-control" name="photo" id="file-input">
                                                <label for="file-input" class="fa fa-pencil shadow text-muted"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-sm-4 col-form-label">Full Name *</label>
                                    <div class="col-md-9 col-sm-8">
                                        <input type="text" name="name" class="form-control form-control-lg"
                                            value="{{auth()->user()->name}}">
                                            @if ($errors->any('name'))
                                            <span class="text-danger">{{$errors->first('name')}}</span>
                                            @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-md-3 col-sm-4 col-form-label">Email *</label>
                                    <div class="col-md-9 col-sm-8">
                                        <input type="text" name="email" class="form-control form-control-lg"
                                            value="{{auth()->user()->email}}">
                                            @if ($errors->any('email'))
                                            <span class="text-danger">{{$errors->first('email')}}</span>
                                            @endif
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn btn-lg btn-light me-2" type="reset">Discard</button>
                                    <button class="btn btn-lg btn-primary" type="submit">Save Changes</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <div id="list-item-2" class="card fieldset border border-muted mt-5">

                    <span class="fieldset-tile text-muted bg-body">Change Password</span>
                    <form action="{{ route('dashboard.change.password') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <h6 class="border-top pt-2 mt-2 mb-3">Change Password</h6>
                                        <div class="mb-3">
                                            <input type="password" name="old_password"
                                                class="form-control form-control-lg" placeholder="Current Password">
                                                @if ($errors->any('old_password'))
                                                <span class="text-danger">{{$errors->first('old_password')}}</span>
                                                @endif
                                        </div>
                                        <div class="mb-1">
                                            <input type="password" name="new_password"
                                                class="form-control form-control-lg" placeholder="New Password">
                                                @if ($errors->any('new_password'))
                                                <span class="text-danger">{{$errors->first('new_password')}}</span>
                                                @endif
                                        </div>
                                        <div>
                                            <input type="password" class="form-control form-control-lg"
                                                placeholder="Confirm New Password" name="confirm_password">
                                            <span class="text-muted small">Minimum 6 characters</span>
                                            @if ($errors->any('confirm_password'))
                                            <span class="text-danger">{{$errors->first('confirm_password')}}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-lg btn-light me-2" type="reset">Discard</button>
                                <button class="btn btn-lg btn-primary" type="submit">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- <div id="list-item-3" class="card fieldset border border-muted mt-5">

                    <span class="fieldset-tile text-muted bg-body">Notifications Settings</span>
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table class="table card-table mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted">Email Notifications</td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_email1"
                                                    checked>
                                                <label class="form-check-label" for="n_email1">Email</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_phone1"
                                                    checked>
                                                <label class="form-check-label" for="n_phone1">Phone</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Billing Updates</td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_email2">
                                                <label class="form-check-label" for="n_email2">Email</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_phone2">
                                                <label class="form-check-label" for="n_phone2">Phone</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">New Team Members</td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_email3">
                                                <label class="form-check-label" for="n_email3">Email</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_phone3">
                                                <label class="form-check-label" for="n_phone3">Phone</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Projects Complete</td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_email4">
                                                <label class="form-check-label" for="n_email4">Email</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_phone4"
                                                    checked>
                                                <label class="form-check-label" for="n_phone4">Phone</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Newsletters</td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_email5">
                                                <label class="form-check-label" for="n_email5">Email</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="n_phone5"
                                                    checked>
                                                <label class="form-check-label" for="n_phone5">Phone</label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-lg btn-light me-2" type="reset">Discard</button>
                            <button class="btn btn-lg btn-primary" type="submit">Save Changes</button>
                        </div>
                    </div>
                </div>
                <div id="list-item-4" class="card fieldset border border-muted mt-5">

                    <span class="fieldset-tile text-muted bg-body">Social Profiles</span>
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="text" class="form-control form-control-lg">
                                <button class="btn btn-info my-1" type="submit"><i
                                        class="fa fa-twitter me-2"></i>Connect to Twitter</button>
                                <div class="small text-muted">One-click sign in</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="text" class="form-control form-control-lg">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Behance</label>
                                <input type="text" class="form-control form-control-lg">
                            </div>
                            <div>
                                <label class="form-label">LinkedIn</label>
                                <input type="text" class="form-control form-control-lg">
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button class="btn btn-lg btn-light me-2" type="reset">Discard</button>
                            <button class="btn btn-lg btn-primary" type="submit">Update Social Profiles</button>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection
