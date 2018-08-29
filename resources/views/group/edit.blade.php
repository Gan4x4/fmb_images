@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::model($group,['route' => ['groups.update',$group->id],'method' => 'PUT']) !!}

        @include('group.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
@endsection