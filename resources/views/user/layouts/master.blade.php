<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title') </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="High academy store" name="keywords">
    <meta content="High academy store" name="description">

    @include('user.layouts.css')


</head>

<body>

    <!-- Navbar Start -->
    <div id="header-ajax">
        @include('user.layouts.nav')
    </div>
    <!-- Navbar End -->

   @yield('content')


    <!-- Footer Start -->
    @include('user.layouts.footer')
    <!-- Footer End -->



    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    @include('user.layouts.js')

</body>

</html>
