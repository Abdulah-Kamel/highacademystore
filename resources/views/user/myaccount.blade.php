@extends('user.layouts.master')

@section('title')
    بيانات حسابى
@endsection

@section('content')
    <style>
        .form-control:focus {
            box-shadow: none;
            border-color: #578FCA;
            border-width: 3px;
        }

        .profile-button {
            background: #578FCA;
            box-shadow: none;
            border: none
        }

        .profile-button:hover {
            background: #387ec9
        }

        .profile-button:focus,
        .profile-button:active {
            background: #387ec9;
            box-shadow: none
        }

        .labels {
            font-size: 1rem;
            font-weight: 900;
        }
    </style>

    <div class="container  mt-5 pt-5 mb-5">
        <div class="row rounded bg-white">
            <div class="col-md-3 border-right">
                <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                    <!-- Profile Image -->
                    <img id="profileImage" class="rounded-circle mt-5" width="150px" height="150px"
                        src="{{ $user->profile_image ? asset('storage/images/user/' . $user->profile_image) : asset('storage/images/pngegg.png') }}">

                    <!-- Hidden File Input -->
                    <input type="file" id="imageUpload" name="profile_image" accept="image/*" hidden>

                    <!-- Custom File Upload Button -->
                    <button type="button" style="background-color: #007bff; color: #fff; border-color: #007bff;"
                        class="btn rounded-pill px-5 mt-3" onclick="document.getElementById('imageUpload').click()">
                        اختر صورة
                    </button>
                    <!-- User Info -->
                    <span class="font-weight-bold my-2"> اهلا, {{ $user->name }}</span>
                    <span class="text-black-50">{{ $user->email }}</span>
                </div>
            </div>

            <div class="col-md-9">
                <div class="p-3 py-2">
                    <h2>تعديل بيانات الحساب</h2>
                    <p class="text-center"><a href="{{ route('user.shipping') }}">تعديل بيانات الشحن الخاصه بيك</a></p>

                    <form id="form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row gy-2 mt-2">
                            <div class="col-md-12">
                                <label class="labels font-bold">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $user->name }}" placeholder="First name">
                            </div>
                            <div class="col-md-12">
                                <label class="labels font-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $user->email }}" placeholder="Enter email id">
                            </div>
                            <div class="col-md-12">
                                <label class="labels font-bold">Mobile Number</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ $user->phone }}" placeholder="Enter phone number">
                            </div>
                            {{-- <div class="col-md-12">
                                    <label class="labels font-bold">ِAddress</label>
                                    <input type="text"
                                        class="form-control" id="ِaddress" name="ِaddress" value="{{ $user->name }}" placeholder="ِAddress">
                                </div> --}}
                            <div class="col-md-12">
                                <label class="labels font-bold">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="password">
                            </div>
                            {{-- <div class="col-md-12">
                                <a href="{{ route('password.request') }}">نسيت كلمة المرور</a>
                            </div> --}}
                        </div>
                </div>
                <div class="my-5 text-center">
                    <button class="btn bg-dropdown-menu profile-button text-white rounded-2" type="submit">Save
                        Profile</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#imageUpload").change(function() {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('profileImage').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
                const formData = new FormData();
                formData.append('profile_image', file);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: '{{ route('user.myaccount.update') }}',
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        Swal.fire('تم تعديل حسابك بنجاح', '', 'success');
                    },

                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessage,
                        });
                    }
                });
            });
            $('#form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: '{{ route('user.myaccount.update') }}',
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        Swal.fire('تم تعديل حسابك بنجاح', '', 'success');
                    },

                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '<br>';
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessage,
                        });
                    }
                });
            });
        });
    </script>
@endsection
