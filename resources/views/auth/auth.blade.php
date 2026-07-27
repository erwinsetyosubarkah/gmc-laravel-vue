<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->club_name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/'. $profile->club_logo) }}">

    @vite(['resources/css/app.css', 'resources/js/auth.js'])
</head>
<body class="hold-transition login-page">
    <div id="app" data-profile="{{ json_encode($profile) }}">
        <login></login>
    </div>
</body>
</html>
