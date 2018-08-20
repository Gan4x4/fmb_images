@extends('layout.common')

@section('sidebar')

    @foreach($image->features as $feature)
        {{ $feature->class }}
    @endforeach
    
@endsection

@section('content')
    <h2>Edit image</h2>
    
    <div class="container-fluid">
       <img class="img-fluid" src='{{$image->getUrl()}}'>
    
    @include('components.errors')
    
    {!! Form::open(['route' => ['images.update',$image->id],'method'=>'PUT','files' => true]) !!}

   
    
    <div class="form-group">
        <label for="class">Class</label>
        {!! Form::select('class',$classes,null,['class'=>"form-control"]) !!}
    </div>
    
        
        
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
    </div>        
    
@endsection

