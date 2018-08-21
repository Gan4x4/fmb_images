@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::model($cat,['route' => ['tags.update',$cat->id],'method' => 'PUT']) !!}

        @include('category.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
    
    
    
    
@endsection