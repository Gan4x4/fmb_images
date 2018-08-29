@extends('layout.common')

@section('sidebar')

    @include('image.sidebar')
    
@endsection

@section('content')
    <h2>Image list</h2>
    
    <div class="container">
        <div class="row">
            @foreach($images as $image) 
                <div class="col">
                    <div class="card" >
                        <a href='{{ route("images.edit",$image->id) }}'>
                            <img class="card-img-top" src="{{ $image->getThumbUrl() }}" alt="Card image cap">
                        </a>
                        <div class="card-body">
                            <p class="card-text">{{ $image->description }}</p>
                            <a href="{{route('images.edit',[$image->id])}}" class="card-link">Edit</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>    
    <hr>
    <br>
    <a href="{{route('images.create')}}" role="button" class="btn btn-primary">Add</a>
    
@endsection
