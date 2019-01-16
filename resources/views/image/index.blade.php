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
                {!! Form::open(['route'=>'images.store','class'=>'form-inline'])  !!}
                <div class='row'>
                    <div class='col-md-12'>
                        @for ($i = 1; $i <= 12; $i++)
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" name="image_nums[]" class="form-check-input" value="{{$i}}" {{ $i == 1 ? 'checked' : '' }}>{{$i}}
                                </label>
                            </div>
                        @endfor
                    </div>
                    <div class='col-md-9'>
                        
                            <label for="url" class="sr-only">URL</label>
                            {!! Form::text('url',null,['class'=>'form-control', 'placeholder' => "URL",'size'=>70]) !!}
                        
                            {!! Form::submit("Parse",['class'=>'btn btn-primary ']) !!}    
                    </div>
                    <div class='col-md-3'>
                        
                    </div>
                </div>
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
