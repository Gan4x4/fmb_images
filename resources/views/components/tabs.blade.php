<ul class="nav nav-tabs">
   
    @php($i = 0)
    @foreach($items as $link => $name)
        <li class="nav-item">
            <a class="nav-link {{ $i++ == $active ? 'active' : '' }}" href="{{ $link }}">{!! $name !!}</a>
        </li>
    @endforeach            
</ul>