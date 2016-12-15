<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dartstracker') }}</title>

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Righteous&text=Dartstracker" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">

    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body class="{{ $class or '' }}">
    <div id="app">
        @include('layouts.nav')

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="/js/scripts.js"></script>
    @yield('javascript')
</body>
</html>
