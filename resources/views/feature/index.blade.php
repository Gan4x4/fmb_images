<ul class="nav flex-column">
     @foreach($features as $feature)
        <li class="nav-item">
            <a href='javascript:void(0)' data='{{ $feature->id }}' class='feature-edit'>{{ $feature->getName() }}</a>
            <span class='small'>
                {{ implode(',',$feature->getDescription()) }}
            </span>
        </li>
    @endforeach
    
</ul>
<hr>

