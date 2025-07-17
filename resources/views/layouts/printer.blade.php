
<!doctype html>
<html class="no-js" lang="">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>BONUS - {{ $distributeurs['nom_distributeur'].' '.$distributeurs['pnom_distributeur'] }}</title>

        @include('layouts.partials._header')

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset('assets/invoice/web/web-base.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/invoice//invoice.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/invoice//invoice-pdf.css') }}">
  <script type="text/javascript" src="{{ asset('assets/invoice/web/scripts.js') }}"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

  <style>
.page-break {
    page-break-after: always;
}
</style>

</head>
    <body>
        <div class="loader-bg"></div>

        @include('layouts.partials._loader')

        <div class="mn-content fixed-sidebar">

            @include('layouts.partials._head')
            @include('layouts.partials._aside-menu')

            <div class="col s6 offset-s6 grid-example">
                <div class="card-content center">
                    <h5><b>@include('flash::message')</b></h5>
                </div>
            </div>

            <div class="col s12">@yield('content')</div>
        </div>
        <div class="left-sidebar-hover"></div>
        @include('layouts.partials._script-footer')
    </body>
</html>
