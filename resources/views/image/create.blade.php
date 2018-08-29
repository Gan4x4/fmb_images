
@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::open(['route' => 'images.store','files' => true,'class' => 'form']) !!}
        {!! Form::file('file') !!}
        {!! Form::bsTextarea('description', 'Description'); !!}
        <hr>
        {!! Form::submit('Upload') !!}
        
    {!! Form::close() !!}
    
@endsection


