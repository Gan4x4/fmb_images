@extends('layouts.common')

@section('content')

    @include('components.errors')
    
    {!! Form::model($item,['route' => ['items.update',$item->id],'method' => 'PUT']) !!}

        @include('item.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
@endsection