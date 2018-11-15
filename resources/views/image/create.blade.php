
@extends('layouts.common')

@section('content')

    @include('components.errors')
    
    {!! Form::open(['route' => 'images.store','files' => true,'class' => 'form']) !!}
        {!! Form::file('file') !!}
        {!! Form::bsText('url', 'Url of Avito advertisement page'); !!}
        {!! Form::bsTextarea('description', 'Description'); !!}
        <hr>
        {!! Form::submit('Upload') !!}
        
    {!! Form::close() !!}
    
@endsection


