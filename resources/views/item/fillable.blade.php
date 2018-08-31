{!! Form::bsText('name','Name') !!}
{!! Form::bsText('description','Description') !!}
{!! Form::bsSelect('parent_id','Parent item',$items) !!}

<h3>Properties</h4>

{!! Form::checklist('properties',$properties,$selected_properties) !!}