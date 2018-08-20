
@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::open(['route' => 'images.store','files' => true]) !!}

        {!! Form::file('file') !!}
        {!! Form::submit('Upload') !!}
        
    {!! Form::close() !!}
    
@endsection


