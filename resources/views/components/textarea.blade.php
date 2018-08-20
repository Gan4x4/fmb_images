<div class='form-group'>
    {!! Form::label($name, $title); !!}
    
    {!! Form::textarea($name,$value,array_merge(['class'=>'form-control','id'=>$name],$attr)); !!}
   
</div>

