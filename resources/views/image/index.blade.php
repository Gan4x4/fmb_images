@extends('layouts.common')

@section('sidebar')

    @include('image.sidebar')
    
@endsection


@section('content')



    <h2>Image list</h2>
    
    @if ( Auth::user()->isAdmin() )
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
    @endif
    
    
    <div class="container-fluid ">
        
        {{ Html::bsTabs($tabs,$active_tab) }}
        
        @include('image.list')

    </div>    
        
@endsection
