<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->club_name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/'. $profile->club_logo) }}">
      <!-- bootstrap.min css -->
  <link rel="stylesheet" href="{{ asset('/') }}vendor/novena/plugins/bootstrap/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('/') }}vendor/novena/plugins/fontawesome-free/css/all.min.css">

  <!-- Icon Font Css -->
  <link rel="stylesheet" href="{{ asset('/') }}vendor/novena/plugins/icofont/icofont.min.css">
  <!-- Slick Slider  CSS -->
  <link rel="stylesheet" href="{{ asset('/') }}vendor/novena/plugins/slick-carousel/slick/slick.css">
  <link rel="stylesheet" href="{{ asset('/') }}vendor/novena/plugins/slick-carousel/slick/slick-theme.css">

  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="{{ asset('/') }}vendor/novena/css/style.css">

@vite(['resources/css/app.css'])
</head>
<body>
    <div id="app" data-profile="{{ json_encode($profile) }}" data-categories="{{ json_encode($categories) }}" data-newevents="{{ json_encode($newevents) }}">
        <layout></layout>
    </div>

        <!-- Main jQuery -->
    <script src="{{ asset('/') }}vendor/novena/plugins/jquery/jquery.js"></script>
    <!-- Bootstrap 4.3.2 -->
    <script src="{{ asset('/') }}vendor/novena/plugins/bootstrap/js/popper.js"></script>
    <script src="{{ asset('/') }}vendor/novena/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="{{ asset('/') }}vendor/novena/plugins/counterup/jquery.easing.js"></script>
    <!-- Slick Slider -->
    <script src="{{ asset('/') }}vendor/novena/plugins/slick-carousel/slick/slick.min.js"></script>
    <!-- Counterup -->
    <script src="{{ asset('/') }}vendor/novena/plugins/counterup/jquery.waypoints.min.js"></script>

    <script src="{{ asset('/') }}vendor/novena/plugins/shuffle/shuffle.min.js"></script>
    <script src="{{ asset('/') }}vendor/novena/plugins/counterup/jquery.counterup.min.js"></script>
    <!-- Google Map -->
    {{-- <script src="{{ asset('/') }}vendor/novena/plugins/google-map/map.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAkeLMlsiwzp6b3Gnaxd86lvakimwGA6UA&callback=initMap"></script> --}}

    <script src="{{ asset('/') }}vendor/novena/js/contact.js"></script>
    @vite(['resources/js/app.js'])
</body>
</html>
