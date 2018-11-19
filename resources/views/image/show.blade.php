@extends('layouts.common')


@section('content')
    <div class='row' >
        <div class='col-8-md'>
            <h2>Image</h2>
        </div>
        
    </div>
    
    <div class="container-fluid">
        
        @if ($link)
            <a href='{{route('images.edit',$image->id)}}'>
                <img class="img-responsive" src='{{ $image->getUrl() }}' >
            </a>
        @else
                <img class="img-responsive" src='{{ $image->getUrl() }}' >
        @endif
        <p>
            {{ $image->description }}
        </p>
        
        @if ($link)
            <a class="btn" href="{{ route('images.edit',[$image->id])}}">Edit</a>
        @else
            {!! Form::open(['route' => ['images.take',$image->id],'method'=>'post']) !!}
                {!! Form::submit('Take it') !!}
            {!! Form::close() !!}
        @endif
        
    </div>        

@endsection