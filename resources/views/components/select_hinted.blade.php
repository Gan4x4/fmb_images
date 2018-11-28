<div class='form-group'>
    {!! Form::label($name, $title); !!}
    
    @php
        if (is_object($values) && get_class($values) == 'Illuminate\Database\Eloquent\Collection'){
            $values = App\Http\Controllers\Controller::collection2select($values);
        }
    @endphp
    
    @if (! empty($hints))
        @php 
            $output = [];
            foreach($hints as $script => $h){
                $out[] = '<a href="javascript:void(0)" class="small" onClick="'.$script.'">'.$h.'</a>';
            }
        @endphp
        {!! implode(', ',$out) !!}
    @endif
    {!! Form::select($name,$values,$selected,App\Helper\Utils::mergeHtmlAttr(['class'=>'form-control','id'=>$name],$attr)); !!}
    
</div>