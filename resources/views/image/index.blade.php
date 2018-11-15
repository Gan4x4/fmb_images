@extends('layouts.common')

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
    
    <div class='row'>
        <div class='col-md-1'>
            <a href="{{route('images.create')}}" role="button" class="btn btn-default">Add</a>
        </div>
        <div class='col-md-11'>
            {!! Form::open(['route'=>'images.store','class'=>'form-inline'])  !!}
                <div class="form-group mx-sm-3 mb-2">
                    <label for="url" class="sr-only">URL</label>
                    {!! Form::text('url',null,['class'=>'form-control', 'placeholder' => "URL",'size'=>70]) !!}
                </div>
             {!! Form::submit("Parse",['class'=>'btn btn-primary mb-2']) !!}
            {!! Form::close() !!}
            
        </div>
    </div>
    <hr>
    
    
    <div class="container-fluid ">
        
        {{ Html::bsTabs($tabs,$active_tab) }}
        
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
                            <hr>
                            @if ( Auth::user()->isAdmin() )
                                {{ $image->user->name }}
                            @endif
                            @if ($image->status == 0 )
                                <i class="fab fa-yelp"></i>
                            @endif
                           
                        </div>
                    </div>
                </div>
            @endforeach
            
           
        </div>
        <hr>
        <div class='row '>
            <div class='col-lg-4 offset-lg-4  d-flex' >
            {{ $images->links() }}
            </div>
        </div>

    </div>    
        
@endsection
