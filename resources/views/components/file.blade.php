<div class='form-group'>
    {!! Form::label($name, $title); !!}
    
    {!! Form::file($name,$value,array_merge(['class'=>'form-control','id'=>$name],$attr)); !!}
</div>
