@extends('layouts.common')

@section('content')

    @include('components.errors')
    
    {!! Form::model($tag,['route' => ['tags.update',$tag->id],'method' => 'PUT']) !!}

        @include('tag.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
    
    
    
    
@endsection