<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->club_name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/'. $profile->club_logo) }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('/') }}vendor/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('/') }}vendor/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/') }}vendor/adminlte/dist/css/adminlte.min.css">
    @vite(['resources/css/auth.css', 'resources/js/auth.js'])
</head>
<body class="hold-transition login-page">
    <div id="app" data-profile="{{ json_encode($profile) }}">
        <layout></layout>
    </div>

    <script src="{{ asset('/') }}vendor/adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('/') }}vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('/') }}vendor/adminlte/dist/js/adminlte.min.js"></script>
</body>
</html>
