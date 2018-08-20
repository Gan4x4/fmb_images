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
                        <img class="card-img-top" src="{{ $image->getThumbUrl() }}" alt="Card image cap">
                        <div class="card-body">
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
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
