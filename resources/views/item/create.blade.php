
@extends('layouts.common')

@section('content')

    @include('components.errors')
    
    {!! Form::open(['route' => 'items.store']) !!}

        @include('item.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
@endsection


