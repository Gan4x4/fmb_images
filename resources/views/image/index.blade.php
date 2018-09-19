@extends('layout.common')

@section('sidebar')

    @include('image.sidebar')
    
@endsection

@section('page-css')
    <style>
        .card-img-top {
            width: 100%;
            height: 15vw;
            object-fit: cover;
        }        
    </style>
@endsection

@section('content')
    <h2>Image list</h2>
    <a href="{{route('images.create')}}" role="button" class="btn btn-primary">Add</a>
    <hr>
    <div class="container-fluid">
        <div class="row">
            @foreach($images as $image) 
                <div class="col-md-3 d-flex"  >
                    <div class="card card-body "  >
                        <a href='{{ route("images.edit",$image->id) }}'>
                            <img class="card-img-top" src="{{ $image->getThumbUrl() }}" alt="Card image cap">
                        </a>
                        <div class="card-body">
                            <!--
                            <p class="card-text">{{ $image->description }}</p>
                            
                            <a href="{{route('images.edit',[$image->id])}}" class="card-link">Edit</a>
                            -->
                            
                            @foreach($image->getFeatureDescription() as $item=>$props)
                                {{ $item }} : 
                                {{ implode(", ",$props) }}
                                <br>
                            @endforeach
                           
                        </div>
                    </div>
                </div>
            @endforeach
            
            {{ $images->links() }}
        </div>
    </div>    
    
    
    
    
@endsection
