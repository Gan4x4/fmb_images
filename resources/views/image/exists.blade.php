@extends('layout.common')


@section('content')
    <div class='row' >
        <div class='col-8-md'>
            <h2>This image(#{{ $image->id }}) already in dataset</h2>
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
    </div>        

@endsection