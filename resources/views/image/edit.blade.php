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
            
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                  <span>Features</span>
                  <a class="d-flex align-items-center text-muted" href="#">
                    <span data-feather="plus-circle"></span>
                  </a>
            </h6>
            <div id="features_list">
                @include('feature.index',['features' => $image->features])
            </div>
            
            <div id="feature_block">
                <!-- place to edit feature properties -->
                
            </div>
        </div>
    </nav>
@endsection

@section('content')
    <div class='row' >
        <div class='col-8-md'>
            <h2>Edit image </h2>
        </div>

        <div class='col-4-md'>
            <button class="btn btn-default reset_coords"><i class="far fa-file"></i> Reset</button>
        </div>
    </div>
    
    <div class="container-fluid">
       <img class="img-responsive" src='{{ $image->getUrl() }}' id='image'>
    </div>        
        {!! Form::model($image,['route' => ['images.update',$image->id],'method'=>'put']) !!}
            {!! Form::bsTextarea('description', 'Description'); !!}
            {!! Form::submit('Save') !!}
        {!! Form::close() !!}
        
        
        
        {!! Form::open(['route' => ['images.destroy',$image->id],'method'=>'delete']) !!}
            <a class="btn btn-danger" onClick="if (confirm('Delete image?')){ $(this).closest('form').submit();}; ">Delete</a>
        {!! Form::close() !!}
        
        
    
@endsection

@section('page-js-script')
    <script src="/jcrop/js/jquery.Jcrop.min.js"></script>
    <script src="/selectize/js/selectize.min.js"></script>
    <script src="/typeahead/bootstrap3-typeahead.min.js"></script>
    
    <script language="Javascript">
        
        var jcrop_api;
            can_disable_save = false;
            
        function showCoords(c)
        {
            // variables can be accessed here as
            // c.x, c.y, c.x2, c.y2, c.w, c.h
            $("#x1").val(c.x);
            $("#y1").val(c.y);
            $("#x2").val(c.x2);
            $("#y2").val(c.y2);
        };
        
        function onSelectionChange(c){
            showCoords(c);
            var disableSave = isSelectionEmpty(c) || isSelectionFull(c);
            //if (can_disable_save){
            $('#save_feature').prop('disabled',disableSave);
            //}
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
        
        function itemOnChangeHandler(){
            var featureId = $("#feature_id").val();
                var itemId = $("#item_id").val();
                $.get( "/api/items/"+itemId+"/properties",{ feature_id : featureId }, function( data ) {
                    $( "#property_block" ).html( data );
                    $('.make_selectized').selectize();
                    enableTaginputs();
                    resetSelection();
                });
        };
        
        
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
            $('#item_id').on('change',itemOnChangeHandler);
            $('#save_feature').on('click',saveFeature);
            //can_disable_save = $('#save_feature').prop('disable');
            //console.log(can_disable_save);
            $('#delete_feature').on('click',deleteFeature);
            $(".reset_coords").on('click',resetSelection); 
            $('.coordinate').on('input',updateSelection);
            $('.make_selectized').selectize();
            enableTaginputs();
            updateSelection();
        }
        
        function setupFeatureList(){
            $('#new_feature').on('click',function () {
                $.get( "/api/images/{{ $image->id }}/features/create/", setupFeatureBlock);                
            });
            
            $('.feature-edit').on('click',function () {
                var featureId = $(this).attr('data');
                $.get( "/api/images/{{ $image->id }}/features/"+featureId+"/edit", setupFeatureBlock);
            });
        }
        
        function afterSave(result) {
            if (result.error === 0){
                // Reload features list
                $.get( "/api/images/{{ $image->id }}/features/", function( data ) {
                    $( "#features_list" ).html( data );
                    setupFeatureList()
                    $( "#feature_block" ).html( "" );
                    jcrop_api.release();
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
            
            setupFeatureList()
            
            $('#image').Jcrop({
                    onSelect: onSelectionChange,
                    onChange: onSelectionChange,
                },function(){
                    jcrop_api = this;
            });
            
           

            
        });
    </script>
@endsection