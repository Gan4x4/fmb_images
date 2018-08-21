
@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::open(['route' => 'tags.store']) !!}

        @include('category.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
@endsection


