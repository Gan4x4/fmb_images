@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::model($property,['route' => ['properties.update',$property->id],'method' => 'PUT']) !!}

        @include('property.fillable')
        {!! Form::submit('Save',['id'=>'save']) !!}
        
    {!! Form::close() !!}
    
   
    
    
@endsection

