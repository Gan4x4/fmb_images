@extends('layouts.common')

@section('page-css')
    <!-- 
        JCrop 
        http://deepliquid.com/content/Jcrop_Manual.html
    -->
    <link rel="stylesheet" href="/jcrop/css/jquery.Jcrop.css" type="text/css" />
    
    <!-- https://github.com/selectize/selectize.js -->
    <link rel="stylesheet" href="/selectize/css/selectize.default.css">
    <link rel="stylesheet" href="/selectize/css/selectize.bootstrap3.css">
    
    <link rel="stylesheet" href="/tagsinput/css/tagsinput.css" type="text/css" />
   
    
@endsection


@section('sidebar')

    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
            
            
            
            <div id="feature_block">
                <!-- place to edit feature properties -->
                
            </div>
        </div>
    </nav>
@endsection

@section('content')
    <div class="container-fluid">
        
        
    
    
       <div class="row" >
           <div class="col-md-9">
                <img class="img-fluid" src='{{ $image->getUrl() }}' id='image' style="max-height: 768px; min-height: 640px">
            </div>
           <div class="col-md-3">
               
                @if($image->width < 500)
                    <img id="preview" class="img-fluid d-none m-2" style="display: block" src='{{ $image->getUrl() }}' id='image' style="max-height: 250px; max-width: 350px">
                    <button  class="btn btn-default" onClick="$('#preview').toggleClass('d-none')" title="Show original"><i class="fas fa-search"></i></button>
                @endif
               
                @if( Auth::check() && ! $image->user)
                    {!! Form::open(['route' => ['images.take',$image->id],'method'=>'post']) !!}
                        {!! Form::submit('Take it',['class'=>'btn btn-primary']) !!}
                    {!! Form::close() !!}
                    <br>
                @endif
                
                Click to add: 
                @php
                    $primary = 'btn-primary';
                @endphp
                @foreach($items as $item)
                    @php
                    
                        if (! $image->hasItem($item->id) ){
                            $new_item_class = $primary; 
                            $primary = '';
                        }
                         $new_item_class = '';
                    @endphp
                    <a href='javascript:void(0)' data='{{ $item->id }}' class='btn  {{ $new_item_class }} feature-add  '>{{ $item->name }}</a>
                @endforeach
                <br>
                <button class="btn btn-default reset_coords"><i class="far fa-file"></i> Reset</button>
                <br>
                <span id='selection_warning' class='badge badge-warning d-none'>Invalid selection </span>
                <hr>
                
                <h4>Features</h4>
                <div id="features_list">
                    @include('feature.index',['features' => $features])
                </div>
           </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h5>Binded images</h5>
            </div>
            <div class="col-md-9">
                #{{ $image->id }}
                @if ($image->user)
                    {{ $image->user->name }}
                @endif
                {{ $image->width }}x{{ $image->height }} 
            </div>
        </div>
        <div class="row">
                @foreach($image->getSiblings() as $img)

                     <div id="image_card_{{ $img->id }}" class="card" style="width:230px">
                         <a href="{{ route('images.edit',[$img->id]) }}"><img class="card-img-top" src='{{ $img->getThumbUrl() }}' id='image'></a>
                         <div class="card-body">
                            #{{ $img->id }} 
                            @if ($img->user)
                                {{ $img->user->name}}
                            @endif
                            {{ $img->width }}x{{ $img->height }} 
                            <a  href="javascript:void(0)" onClick="deleteImagCardEntity({{ $img->id }})"><i class="fas fa-trash"></i></a> 
                            <span class="small">
                                {{ implode(', ',array_keys($img->getFeatureDescription())) }}
                            </span>
                         </div>
                     </div>

                 @endforeach
        </div>
       
        @if ($image->source )
            <a href="{{ $image->source->link }}">{{ $image->source->link }}</a>
        @endif
        
        
        @if ( $image->user || Auth::user()->isAdmin())
            {!! Form::model($image,['route' => ['images.update',$image->id],'method'=>'put']) !!}
                {!! Form::bsTextarea('description', 'Description'); !!}
                {!! Form::bsCheckbox('validation', 'Validation',1,$image->validation); !!}
                {!! Form::submit('Save') !!}
            {!! Form::close() !!}
        
            {!! Form::open(['route' => ['images.destroy',$image->id],'method'=>'delete']) !!}
                <a class="btn btn-danger" onClick="if (confirm('Delete image?')){ $(this).closest('form').submit();}; ">Delete</a>
            {!! Form::close() !!}
        @else
            <p>
                {{ $image->description }}
            </p>
            
        @endif
        
       
        
    </div>            
    
@endsection

@section('page-js-script')
    <script src="/jcrop/js/jquery.Jcrop.min.js"></script>
    <script src="/selectize/js/selectize.min.js"></script>
    <script src="/typeahead/bootstrap3-typeahead.min.js"></script>
    
    <script language="Javascript">
        
        var jcrop_api;
            can_disable_save = false;
            
            
        function deleteImagCardEntity(id){
                if (confirm("Delete")){
                    var path = '/images/'+id+'/ajax';
                    $.ajax({
                    url: path,
                    type: 'DELETE',  
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                success: function(result) {
                    if (result.error === 0){
                        $("#image_card_"+id).remove();
                    }else{
                        alert(result.message);
                    }
                }
            });
        }
    }
            
        function showCoords(c)
        {
            // variables can be accessed here as
            // c.x, c.y, c.x2, c.y2, c.w, c.h
            $("#x1").val(Math.round(c.x));
            $("#y1").val(Math.round(c.y));
            $("#x2").val(Math.round(c.x2));
            $("#y2").val(Math.round(c.y2));
        };
        
        function onSelectionChange(c){
            showCoords(c);
            var disableSave = isSelectionEmpty(c) || isSelectionFull(c);
            //console.log(disableSave);
            if (disableSave){
                $('#save_feature').addClass('disabled');
                $('#selection_warning').removeClass('d-none');
            }else{
                $('#save_feature').removeClass('disabled');
                $('#selection_warning').addClass('d-none');
            }
        }
        
        function resetSelection(){
            jcrop_api.release();
            onSelectionChange({x:0,y:0,x2:0,y2:0});
        }
        
        function isSelectionEmpty(c){
            return c.x >= c.x2 || c.y >= c.y2;
        }
        
        function isSelectionFull(c){
            return $("#x1").attr('min') == c.x &&
                   $("#y1").attr('min') == c.y && 
                   $("#x2").attr('max') == c.x2 &&
                   $("#y2").attr('max') == c.y2;
        }
        
        function updateSelection(){
            var $form = $('#feature_form');
                 jcrop_api.setSelect([
                     $form.find('#x1').val(),
                     $form.find('#y1').val(),
                     $form.find('#x2').val(),
                     $form.find('#y2').val()
                ]);
        }
        /*
        function itemOnChangeHandler(){
            var featureId = $("#feature_id").val();
                var itemId = $("#item_id").val();
                $.get( "/api/items/"+itemId+"/properties",{ feature_id : featureId, image_id: {{ $image->id }}  }, function( data ) {
                    $( "#property_block" ).html( data );
                    $('.make_selectized').selectize();
                    enableTaginputs();
                });
        };
        */
        
        function enableTaginputs(){
            $(".tag_input").each(function(i,val){
               var data = JSON.parse($(this).attr('data'));
               $(".tag_input").typeahead({ 
                    source: data 
                });
            });
        }
        
        function setupFeatureBlock(data){
            $( "#feature_block" ).html( data );
            //$('#item_id').on('change',itemOnChangeHandler);
            $('#save_feature').on('click',saveFeature);
            //can_disable_save = $('#save_feature').prop('disable');
            //console.log(can_disable_save);
            $('#delete_feature').on('click',deleteFeature);
            $(".reset_coords").on('click',resetSelection); 
            $('.coordinate').on('input',updateSelection);
            $('.make_selectized').selectize();
            enableTaginputs();
        }
        
        
        function setupFeatureList(){
           
            
            $('.feature-edit').on('click',function () {
                var featureId = $(this).attr('data');
                $.get( "/api/images/{{ $image->id }}/features/"+featureId+"/edit", function( data ){
                        setupFeatureBlock(data);
                        updateSelection();
                });
            });
            
        }
        /*
        function setupFeatureList(){
            $('#new_feature').on('click',function () {
                $.get( "/api/images/{{ $image->id }}/features/create/", function( data ){
                    if ( $( "#feature_block" ).html()){
                        // Call from another feature edit
                        setupFeatureBlock(data);
                        resetSelection();
                    }
                    else{
                        // Call from empty page
                        setupFeatureBlock(data);
                        onSelectionChange(jcrop_api.tellSelect());
                    }
                });                
            });
            
            $('.feature-edit').on('click',function () {
                var featureId = $(this).attr('data');
                $.get( "/api/images/{{ $image->id }}/features/"+featureId+"/edit", function( data ){
                        setupFeatureBlock(data);
                        updateSelection();
                });
            });
            
        }
        */
        function afterSave(result) {
            if (result.error === 0){
                // Reload features list
                resetSelection();
                $.get( "/api/images/{{ $image->id }}/features/", function( data ) {
                    $( "#features_list" ).html( data );
                    setupFeatureList();
                    $( "#feature_block" ).html( "" );
                    $('#selection_warning').addClass('d-none');
                });
            }else{
                alert(result.message);
            }
        }
        
        
        function deleteFeature(){
            var featureId = $("#feature_id").val();
            if ( confirm("Delete ?") ){
                $.ajax({
                    url: "/api/images/{{ $image->id }}/features/"+featureId,
                    type: 'DELETE',  
                    dataType: 'json',
                    success: afterSave
                   });
            }
        }
        
        
        function saveFeature(){
            var featureId = $("#feature_id").val();
            if (featureId){
                // Update
                $.ajax({
                    url: "/api/images/{{ $image->id }}/features/"+featureId,
                    type: 'PUT',  
                    data:  $('#feature_form').serialize(),
                    dataType: 'json',
                    success: afterSave
                });
            }
            else{
                // Create
                $.ajax({
                    url: "/api/images/{{ $image->id }}/features",
                    type: 'POST',  
                    data:  $('#feature_form').serialize(),
                    dataType: 'json',
                    success:afterSave
                });
            }
        }
        
        
        jQuery(function($) {
            
             $('.feature-add').on('click',function () {
                var  item_id = $(this).attr('data');
                $.get( "/api/images/{{ $image->id }}/features/create/", {item_id : item_id},function( data ){
                    //console.log($( "#feature_block" ).html());
                    //if ( $( "#feature_block" ).html()){
                        // Call from another feature edit
                    //    setupFeatureBlock(data);
                    //    resetSelection();
                    //}
                    //else{
                        // Call from empty page
                        setupFeatureBlock(data);
                        onSelectionChange(jcrop_api.tellSelect());
                    //}
                });                
            });
            
            setupFeatureList()
            
            $('#image').Jcrop({
                    onSelect: onSelectionChange,
                    onChange: onSelectionChange,
                    trueSize: [{{ $image->width }},{{ $image->height }}]
                },function(){
                    jcrop_api = this;
            });
            
           

            
        });
    </script>
@endsection