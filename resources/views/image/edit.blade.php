@extends('layout.common')

@section('page-css')
    <!-- 
        JCrop 
        http://deepliquid.com/content/Jcrop_Manual.html
    -->
    <link rel="stylesheet" href="/jcrop/css/jquery.Jcrop.css" type="text/css" />
@endsection


@section('sidebar')

    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
            
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                  <span>Features</span>
                  <a class="d-flex align-items-center text-muted" href="#">
                    <span data-feather="plus-circle"></span>
                  </a>
            </h6>
            
            <ul class="nav flex-column">
                @foreach($image->features as $feature)
                    <li class="nav-item">
                        <a href='javascript:void(0)' data='{{ $feature->toJson() }}' class='feature-edit'>{{ $feature->getName() }}</a>
                    </li>
                    
                @endforeach
                
                <li class="nav-item">
                    <a href='javascript:void(0)' data='{ "id": 0, "tag_id": 1, "region": "{{ $image->size2region() }}" }' class='feature-edit'>New</a>
                </li>
            </ul>
            
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                  <span>Add feature</span>
                  <a class="d-flex align-items-center text-muted" href="#">
                    <span data-feather="plus-circle"></span>
                  </a>
            </h6>

            {!! Form::open(['route' => ['images.update',$image->id],'method'=>'PUT','id'=>'feature-edit-form']) !!}
                {{ Form::hidden('feature_id',0,['id'=>'feature_id']) }}
                {{ Form::bsSelect('tag_id','Tag',$tags) }}
                {{ Form::bsText('x1','X1') }}
                {{ Form::bsText('y1','Y1') }}
                {{ Form::bsText('x2','X2') }}
                {{ Form::bsText('y2','Y2') }}
                {!! Form::submit('Save') !!}
            {!! Form::close() !!}
        </div>
    </nav>
@endsection

@section('content')
    <h2>Edit image</h2>
    
    <div class="container-fluid">
       <img class="img-fluid" src='{{$image->getUrl()}}' id='image'>
    
    @include('components.errors')
    
    {!! Form::open(['route' => ['images.update',$image->id],'method'=>'PUT','files' => true]) !!}

   
    
    <div class="form-group">
        <label for="class">Class</label>
        {!! Form::select('class',$tags,null,['class'=>"form-control"]) !!}
    </div>
    
        
        
        {!! Form::submit('Save') !!}
        
    {!! Form::close() !!}
    
    </div>        
    
@endsection

@section('page-js-script')
    <script src="/jcrop/js/jquery.Jcrop.min.js"></script>
    
    
    <script language="Javascript">
        
        function showCoords(c)
        {
            // variables can be accessed here as
            // c.x, c.y, c.x2, c.y2, c.w, c.h
            $("#x1").val(c.x);
            $("#y1").val(c.y);
            $("#x2").val(c.x2);
            $("#y2").val(c.y2);
        };
        
        jQuery(function($) {
            
            
            $('.feature-edit').on('click',function () {
                var $form = $('#feature-edit-form');
                var $data = JSON.parse($(this).attr('data'));
                console.info($data);
                $form.find('#feature_id').val($data.id);
                $form.find('#tag_id').val($data.tag_id);
                //console.log($data.tag_id);
                var coords = JSON.parse($data.region);
                console.info(coords);
                
                $form.find('#x1').val(coords[0][0]);
                $form.find('#y1').val(coords[0][1]);
                $form.find('#x2').val(coords[1][0]);
                $form.find('#x2').val(coords[1][1]);
                
                $('#image').Jcrop({
                    onSelect: showCoords,
                    onChange: showCoords,
                    setSelect: [coords[0][0],coords[0][1],coords[1][0],coords[1][1]]
                });
                
            });
            
        });
    </script>
@endsection