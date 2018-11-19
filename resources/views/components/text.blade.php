<div class='form-group'>
    {!! Form::label($name, $title); !!}
    
    {!! Form::text($name,$value,App\Helper\Utils::mergeHtmlAttr(['class'=>'form-control','id'=>$name],$attr)); !!}
   
</div>

