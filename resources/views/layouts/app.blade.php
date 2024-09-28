<!DOCTYPE html>
<html lang="tr">

<head>
    @include('layouts.header')
</head>

<body>
    @yield('content')
    @include('layouts.footer')
    @yield('js')
</body>

</html>
