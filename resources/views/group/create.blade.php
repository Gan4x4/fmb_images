
@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::open(['route' => 'groups.store']) !!}

        @include('group.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
@endsection


