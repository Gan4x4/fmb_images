<ul style="list-style-type:none" class="nav flex-column">
    @php( $i_count = $p_count = 0 )
@foreach($items as $item)
    <li class="nav-item">
    
        {!! Form::checkbox('items[]',$item->id) !!}
        {{ $item->name }} {{  $item->count() }}
        @php( $i_count += $item->count() )
        <ul style="list-style-type:none" >
            @foreach($item->properties as $property)
                <li>
                    {!! Form::checkbox($item->id.'_propertys[]',$property->id) !!} 
                    
                    @php($p_count += $property->count())
                    @php($list_id = $item->id.'_'.$property->id.'_list')
                    
                    {{ $property->name }} {{ $property->count() }} <a href='#{{ $list_id }}' data-toggle="collapse" ><i class="fas fa-angle-down"></i></a>

                    <ul id="{{ $list_id }}" style="list-style-type:none" class="collapse">
                        @php( $item_tags = $property->getItemTags($item->id) )
                        @php( $filled = 0 )
                        @foreach( $item_tags as $tag )
                        <li>
                            {!! Form::checkbox($item->id.'_'.$property->id.'_tags[]',$tag->id) !!} 
                            {{ $tag->name }} {{ $tag->count }} 
                            @php( $filled += $tag->count )
                        </li>
                        @endforeach
                        <li>
                            {!! Form::checkbox($item->id.'_'.$property->id.'_tags[]',0) !!} 
                            Undefined {{  $item->count() - $filled }} 
                        </li>
                    </ul>
                    
                    
                    
                   
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
</ul>

Total: {{ $i_count }} items ;  {{ $p_count }} tags


@section('page-js-script')

    @parent

    <script>
        
        jQuery(function($) {
            $('input[type="checkbox"]').on('change',function(){
                var checked = $(this).prop('checked');
                if (checked){
                    var $parent_li = $(this).parents('li');
                    $parent_li.each(  function(index, item){
                        $(item).find('input[type="checkbox"]').first().prop('checked',true);
                    });
                    
                }else{
                    $(this).parent().find('input[type="checkbox"]').prop('checked',false);
                }
                
                if (typeof afterUpdate === "function"){
                    afterUpdate();
                }
                
                 
            });
        });
    
    </script>
@endsection

