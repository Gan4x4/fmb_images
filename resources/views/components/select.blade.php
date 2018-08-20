<div class='form-group'>
    {!! Form::label($name, $title); !!}
    
    {!! Form::select($name,$values,$selected,array_merge(['class'=>'form-control','id'=>$name],$attr)); !!}
    
</div>