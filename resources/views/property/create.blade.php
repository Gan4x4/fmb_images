@extends('layouts.common')

@section('content')

    @include('components.errors')
    
    {!! Form::open(['route' => 'properties.store']) !!}

        @include('property.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
@endsection



