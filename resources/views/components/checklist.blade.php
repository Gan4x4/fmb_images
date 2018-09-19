<div class='row'>
    @foreach($objects as $object)
        <div class='col-sm-4 col-md-3'>
            {!! Form::bsCheckbox($name.'[]',$object->name,$object->id,in_array($object->id,$selected_id)) !!}
        </div>
    @endforeach
</div>
