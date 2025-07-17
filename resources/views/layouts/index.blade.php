
<!DOCTYPE html>
<html lang="fr">
    <head>

        @include('layouts.partials._header')

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