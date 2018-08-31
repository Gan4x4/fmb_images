@foreach($objects as $object)
    {!! Form::bsCheckbox($name.'[]',$object->name,$object->id,in_array($object->id,$selected_id)) !!}
@endforeach
