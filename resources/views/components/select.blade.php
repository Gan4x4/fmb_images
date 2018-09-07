<div class='form-group'>
    {!! Form::label($name, $title); !!}
    
    @php
        if (is_object($values) && get_class($values) == 'Illuminate\Database\Eloquent\Collection'){
            $values = App\Http\Controllers\Controller::collection2select($values);
        }
    @endphp
    
    
    {!! Form::select($name,$values,$selected,array_merge(['class'=>'form-control','id'=>$name],$attr)); !!}
    
</div>