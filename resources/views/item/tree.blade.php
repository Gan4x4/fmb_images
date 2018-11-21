<ul style="list-style-type:none" class="nav flex-column">
@foreach($items as $item)
    <li class="nav-item">
    
    {!! Form::checkbox('items[]',$item->id) !!}
    {{ $item->name }} {{ $item->count() }}
    <ul style="list-style-type:none" >
    @foreach($item->properties as $property)
        <li >
            {!! Form::checkbox($item->id.'_propertys[]',$property->id) !!} 
            {{ $property->name }} {{ $property->count() }}
        </li>
    @endforeach
    </ul>
    </li>
@endforeach
</ul>





