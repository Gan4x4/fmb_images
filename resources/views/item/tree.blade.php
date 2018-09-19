<ul style="list-style-type:none" class="nav flex-column">
@foreach($items as $item)
    <li class="nav-item">
    
    {!! Form::checkbox('items[]',$item->id) !!}
    {{ $item->name }}
    <ul style="list-style-type:none" >
    @foreach($item->properties as $property)
        <li >
            {!! Form::checkbox($item->id.'_propertys[]',$property->id) !!}
            {{ $property->name }}
        </li>
    @endforeach
    </ul>
    </li>
@endforeach
</ul>





