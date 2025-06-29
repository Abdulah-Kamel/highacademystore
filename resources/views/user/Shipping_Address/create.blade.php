@extends('user.layouts.master')

@section('title')
    تعديل بيانات الشحن
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
            <div class="col-md-12">
                <div class="p-3 py-2">
                    <h4 class="text-center">تعديل بيانات الشحن</h4>
                    <form id="form">
                        <div class="row gy-2 mt-2">
                            @csrf
                            <div class="form-group">
                                <label for="governorates">اختر المحافظة</label>
                                <select class="form-control" id="governorates" name="governorate"
                                    onchange="calculateTotal()">
                                    <option value="">اختر المحافظة</option>
                                    @foreach ($governoratesData as $governorate)
                                        <option value="{{ $governorate['governorate_name_ar'] }}"
                                            data-id="{{ $governorate['id'] }}" gov-price="{{ $governorate['price'] }}">
                                            {{ $governorate['governorate_name_ar'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cities">اختر المدينة</label>
                                <select class="form-control" id="cities" name="city" disabled>
                                    <option value="">اختر المدينة</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="address">العنوان التفصيلي</label>
                                <input class="form-control" id="address" name="address" placeholder="العنوان التفصيلي" />
                            </div>

                            <div class="form-group">
                                <label for="user_name">الاسم ثلاثي (كما في البطاقة)</label>
                                <input class="form-control" id="user_name" name="name" placeholder="اسم المستلم"
                                    required />
                            </div>

                            <div class="form-mobile">
                                <label for="mobile">رقم الموبيل</label>
                                <input class="form-control" type="number" id="mobile" name="mobile" pattern="\d{11}"
                                    minlength="11" maxlength="11" placeholder="رقم موبايل المستلم" required />
                            </div>

                            <div class="form-mobile">
                                <label for="temp_mobile">رقم الموبيل الاحتياطي</label>
                                <input class="form-control" type="number" id="temp_mobile" name="temp_mobile"
                                    pattern="\d{11}" minlength="11" maxlength="11" placeholder="رقم موبايل الاحتياطي"
                                    required />
                            </div>

                            <div class="form-group">
                                <label for="near_post">اسم اقرب مكتب بريد</label>
                                <input class="form-control" id="near_post" name="near_post"
                                    placeholder="اسم اقرب مكتب بريد" />
                            </div>
                        </div>
                </div>
            </div>
            <div class="my-5 text-center">
                <button class="btn bg-dropdown-menu profile-button text-white rounded-2" type="submit">Save
                </button>
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
            $('#form').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: '{{ route('user.useraddress.store') }}',
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم أضافه البيانات بنجاح',
                            showConfirmButton: false,
                        });
                        setTimeout(function() {
                            window.location.href = document.referrer ||
                                '{{ route('user.card.data') }}';
                        }, 2000);
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const citiesData = @json($citiesData);
            const governoratesSelect = document.getElementById('governorates');
            const citiesSelect = document.getElementById('cities');
            const accordion = document.getElementById('accordionExample');

            governoratesSelect.addEventListener('change', function() {
                const governorateId = this.selectedOptions[0].getAttribute('data-id');
                citiesSelect.innerHTML = '<option value="">اختر المدينة</option>';
                citiesSelect.disabled = !governorateId;

                if (governorateId) {
                    const filteredCities = citiesData.filter(city => city.governorate_id === governorateId);
                    filteredCities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.city_name_ar;
                        option.textContent = city.city_name_ar;
                        citiesSelect.appendChild(option);
                    });
                }
                updateFormState();
            });

            citiesSelect.addEventListener('change', updateFormState);

            function updateFormState() {
                const governorate = governoratesSelect.value;
                const city = citiesSelect.value;
                accordion.classList.toggle('hidden', !governorate || !city);
            }

        });
    </script>
@endsection
