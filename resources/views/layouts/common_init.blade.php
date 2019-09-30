<!DOCTYPE html>
<html lang="ja">
    <head>
        @yield('head_config')
        @yield('pageCss')
    </head>
    <body onload="init()">
        @yield('content')
        @yield('footer')
        @yield('js_config')
    </body>
</html>