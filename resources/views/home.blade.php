<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600" rel="stylesheet">

    <title>{{ config('app.name') }}</title>

    <script>
      window.app = {
        name: "{{ config('app.name') }}",
        user: {!! json_encode(auth()->user()->only(['id', 'name'])) !!}
      }
    </script>
</head>
<body>
<div id="app">
    <app/>
</div>
<script src="https://unpkg.com/ionicons@4.2.2/dist/ionicons.js"></script>
<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
