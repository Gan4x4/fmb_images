<ul style="list-style-type:none" class="nav flex-column">
    @php( $i_count = $p_count = 0 )
@foreach($items as $item)
    <li class="nav-item">
    
        {!! Form::checkbox('items[]',$item->id) !!}
        {{ $item->name }} {{  $item->count() }}
        @php( $i_count += $item->count() )
        <ul style="list-style-type:none" >
            @foreach($item->properties as $property)
                <li >
                    {!! Form::checkbox($item->id.'_propertys[]',$property->id) !!} 
                    {{ $property->name }} {{ $property->count() }}
                    @php( $p_count += $property->count() )
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
</ul>

Total: {{ $i_count }} items ;  {{ $p_count }} tags




