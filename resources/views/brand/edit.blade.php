@extends('layout.common')

@section('content')

    @include('components.errors')
    
    {!! Form::model($brand,['route' => ['brands.update',$brand->id],'method' => 'PUT']) !!}

        @include('brand.fillable')
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
@endsection