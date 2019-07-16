<ul style="list-style-type:none" class="nav flex-column">
    @php( $i_count = $p_count = 0 )
    @php( $val_features = isset($validation) ? App\Feature::getIdsOfValidationFeatures() : false )
@foreach($items as $item)
    <li class="nav-item">
    
        {!! Form::checkbox('items[]',$item->id,isset($tree[$item->id])) !!}
        {{ $item->name }} {{  $item->count($val_features) }}
        @php( $i_count += $item->count($val_features) )
        <ul style="list-style-type:none" >
            @foreach($item->properties as $property)
                <li>
                    {!! Form::checkbox($item->id.'_propertys[]',$property->id,isset($tree[$item->id][$property->id])) !!} 
                    
                    @php($p_count += $property->count( $val_features ))
                    @php($list_id = $item->id.'_'.$property->id.'_list')
                    
                    
                    {{ $property->name }} {{ $property->count($val_features) }} <a href='#{{ $list_id }}' data-toggle="collapse" ><i class="fas fa-angle-down"></i></a>
                    @php( $has_checked_tags =  ! empty($tree[$item->id][$property->id]))

                    <ul id="{{ $list_id }}" style="list-style-type:none" class="{{ $has_checked_tags ? '' : 'collapse' }}">
                        @php( $item_tags = $property->getItemTags($item->id,$val_features) )
                        @php( $filled = 0 )
                        @foreach( $item_tags as $tag )
                        <li>
                            @php($checked = isset($tree[$item->id][$property->id]) ? in_array($tag->id,$tree[$item->id][$property->id]) : false)
                            {!! Form::checkbox($item->id.'_'.$property->id.'_tags[]',$tag->id, $checked) !!} 
                            {{ $tag->name }} {{ $tag->count }} 
                            @php( $filled += $tag->count )
                        </li>
                        @endforeach
                        <li>
                            @php($checked = isset($tree[$item->id][$property->id]) ? in_array(0,$tree[$item->id][$property->id]) : false)
                            {!! Form::checkbox($item->id.'_'.$property->id.'_tags[]',0,$checked) !!} 
                            Undefined {{  $item->count( $val_features ) - $filled }} 
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
                    
                    $(this).siblings('ul')
                            .removeClass('collapse')
                            .find('input[type="checkbox"]')
                            .prop('checked',true);
                    
                    
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

