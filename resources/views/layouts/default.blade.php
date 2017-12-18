<html>
<head>
    @include('includes.head')
</head>

<body>
  @include('includes.header')

<div class="container" style="min-height:100%;">



    <div id="main" class="row" style="margin-left:1%">
            @yield('content')
    </div>

    <footer class="row">
        @include('includes.footer')
    </footer>

</div>
</body>
</html>
