<script src="{{url('/')}}/admin/assets/js/theme.js"></script>

    <script src="{{url('/')}}/admin/assets/js/bundle/apexcharts.bundle.js"></script>

    <script src="{{url('/')}}/admin/assets/js/bundle/apexcharts.bundle.js"></script>
    <!-- jQuery 3 -->
    <script src="{{url('/')}}/admin/assets/js/jquery.min.js"></script>
    {{-- <script src=" {{url('/')}}/admin/assets/js/toastr.js"></script> --}}

    <script src="{{ url('/') }}/admin/assets/js/datatables.min.js"></script>


    <script src="{{url('/')}}/admin/assets/js/bundle/sweetalert2.bundle.js"></script>

    {{-- jq validation --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

    {{-- <script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
    <script type="text/javascript"
    src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/localization/messages_ar.js"></script> --}}
    <script src="{{url('/')}}/admin/assets/js/bundle/dataTables.bundle.js"></script>
    {{-- <link rel="stylesheet" href="{{url('/')}}/admin/assets/dist/jquery.toast.min.js"> --}}
    <script src="{{url('/')}}/admin/assets/js/bundle/dropify.bundle.js"></script>
    <script src="{{url('/')}}/admin/assets/js/bundle/select2.bundle.js"></script>
    <!-- JavaScript -->
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>


    @yield('js')
    <script>
        $('.select2').select2();
      $(function () {
        $('.dropify').dropify();
        var drEvent = $('#dropify-event').dropify();
        drEvent.on('dropify.beforeClear', function (event, element) {
          return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
        });
        drEvent.on('dropify.afterClear', function (event, element) {
          alert('File deleted');
        });
        $('.dropify-fr').dropify({
          messages: {
            default: 'Glissez-dÃ©posez un fichier ici ou cliquez',
            replace: 'Glissez-dÃ©posez un fichier ou cliquez pour remplacer',
            remove: 'Supprimer',
            error: 'DÃ©solÃ©, le fichier trop volumineux'
          }
        });
      });
    </script>
    <script>
        // LUNO Revenue
    var options = {
      series: [{
        name: 'Income',
        data: [440, 505, 414, 671, 227, 413, 201, 352, 752, 320, 257, 160]
      }, {
        name: 'Expenses',
        data: [123, 142, 135, 127, 143, 122, 117, 131, 122, 122, 112, 116]
      }, {
        name: 'Revenue',
        data: [223, 242, 235, 227, 243, 222, 217, 231, 222, 222, 212, 216]
      }],
      chart: {
        type: 'bar',
        height: 260,
        stacked: true,
        stackType: '100%',
        toolbar: {
          show: false,
        },
      },
      colors: ['var(--chart-color1)', 'var(--chart-color2)', 'var(--chart-color3)'],
      responsive: [{
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom',
            offsetX: -10,
            offsetY: 0
          }
        }
      }],
      xaxis: {
        categories: ['Jan', 'Feb', 'March', 'Apr', 'May', 'Jun', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
      },
      fill: {
        opacity: 1
      },
      dataLabels: {
        enabled: false,
      },
      legend: {
        position: 'bottom',
        horizontalAlign: 'center',
      },
    };
    var chart = new ApexCharts(document.querySelector("#apex-AudienceOverview"), options);
    chart.render();
    // Sales by Category
    var options = {
      chart: {
        height: 280,
        type: 'donut',
      },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              total: {
                showAlways: true,
                show: true
              }
            }
          }
        }
      },
      dataLabels: {
        enabled: false,
      },
      legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        show: true,
      },
      colors: ['var(--chart-color1)', 'var(--chart-color2)', 'var(--chart-color3)'],
      series: [55, 35, 10],
    }
    var chart = new ApexCharts(document.querySelector("#apex-SalesbyCategory"), options);
    chart.render();
    </script>
