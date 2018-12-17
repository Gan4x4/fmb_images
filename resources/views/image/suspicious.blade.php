@extends('layouts.common')

@section('sidebar')

 
    
@endsection


@section('content')


    @foreach($features as $f)
        {{ $f->getName() }} <a href="{{ route('images.show',[$f->image_id]) }}">{{ $f->getName() }}</a>
        <br>
    
    @endforeach
 
@endsection