{!! Form::bsText('name','Tag name') !!}
{!! Form::bsText('description','Tag description') !!}

<h3>Properties</h4>
{!! Form::checklist('properties',$properties,$selected_properties) !!}

