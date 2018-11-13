@auth
    <ul class="navbar-nav ">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ Auth::user()->name }}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
                    Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>

                <!-- Admin
                if ( Auth::user()->isAdmin() && __('menu.admin') != 'menu.admin' )
                     
                    foreach( __('menu.admin') as $url => $title)
                        <a class="dropdown-item" href=" $url "> $title </a>
                    endforeach
                endif
                -->
                

            </div>
        </li>

</ul>
@else
    <!-- Not logged user -->
    @foreach( __('menu.unlogged') as $url => $title)
        <a class="nav-item nav-link " href="{{ $url }}">{{ $title }}</a>
    @endforeach
@endauth
