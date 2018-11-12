<ul class="nav flex-column">
     @foreach($features as $feature)
        <li class="nav-item">
            <a href='javascript:void(0)' data='{{ $feature->id }}' class='feature-edit'>{{ $feature->getName() }}</a>
        </li>
    @endforeach
    
</ul>
<hr>
<button class="btn btn-default" id='new_feature'>Add</button>