<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
         <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>FMB.Images</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
        
 

        <!-- Styles 
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}" type="text/css">
        
        <!-- Custom css -->
        @yield('page-css')
    </head>
    <body>
        <div id='app'> <!-- Start vue app block -->

            <header>
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand" href="/"> Image DB for FMB.</a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            @foreach( __('menu.common') as $url => $title)
                                <a class="nav-item nav-link {{ request()->url() == $url ? 'active' : '' }}" href="{{ $url }}">{{ $title }}</a>
                            @endforeach
                            @if (Auth::check() && Auth::user()->isAdmin())
                                @foreach( __('menu.admin') as $url => $title)
                                    <a class="nav-item nav-link {{ request()->url() == $url ? 'active' : '' }}" href="{{ $url }}">{{ $title }}</a>
                                @endforeach
                            @endif
                            
                        </ul>
                        
                        <div class="navbar-nav ml-auto">
                            @include('auth.menu')
                        </div>
                        
                    </div>
                   
                </nav>
            </header>

            <div class="container-fluid content">
            <div class="row">     
                @yield('sidebar')
                
                
                
                <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
                    @yield('content')
                </main>
           </div> 
        </div>
        </div> <!-- End Vue app block -->
        <!-- Bootstrap links -->
        <!--
            Slim build don't contain ajax function :(
            <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        -->
        
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
        <!-- TODO integrate it  -->
        <script src="{{ asset('js/gan.js') }}"></script>
        @yield('page-js-script')
        
    </body>
</html>
