
@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::open(['route' => 'brands.store']) !!}

        @include('brand.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
@endsection


