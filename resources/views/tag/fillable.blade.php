{!! Form::bsText('name','Tag name') !!}
{!! Form::bsText('description','Tag description') !!}

<h3>Groups</h4>
@foreach($groups as $group)
    {!! Form::bsCheckbox('groups[]',$group) !!}
@endforeach


