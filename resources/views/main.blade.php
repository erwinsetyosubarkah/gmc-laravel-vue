<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->club_name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/'. $profile->club_logo) }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app" data-profile="{{ json_encode($profile) }}" data-categories="{{ json_encode($categories) }}" data-newevents="{{ json_encode($newevents) }}">
        <layout></layout>
    </div>
</body>
</html>
