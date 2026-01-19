<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Global Education Hub</title>
    <link rel="stylesheet" href="{{ asset('/css/home.css') }}">
    <!-- <link rel="stylesheet" href="responsive.css"> -->
</head>

<body >
   
        <!-- Header -->
        @include('layouts.header')
         @yield('content') 
        

    <script src="{{ asset('/js/home.js') }}"></script>
</body>

</html>