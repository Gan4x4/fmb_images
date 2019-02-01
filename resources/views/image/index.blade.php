@extends('layouts.common')

@section('sidebar')

    @include('image.sidebar')
    
@endsection


@section('content')



    <h2>Image list</h2>
    
    @if ( Auth::user()->isAdmin() )
        <div class='row'>
            <div class='col-md-2'>
                <a href="{{route('images.create')}}" role="button" class="btn btn-default">Add</a>
                <br>
                <a href="{{route('images.suspicious')}}">Find suspicious</a>
            </div>
            <div class='col-md-10'>
                @include('image.add')
            </div>
        </div>
        <hr>
    @endif
    
    
    <div class="container-fluid ">
        
        {{ Html::bsTabs($tabs,$active_tab) }}
        
        @include('image.list')

    </div>    
        
@endsection
