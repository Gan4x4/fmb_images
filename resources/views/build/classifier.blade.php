@extends('layouts.common')

@section('sidebar')

    ----
    
@endsection

@section('content')
    
    {{ Html::bsTabs($menu,2) }}
    {!! Form::open(['route'=>['builds.store']]) !!}
    <div class='row'>
        <div id='tree' class='col-md-8'>
            @include('item.tree',['items'=>$items])
        </div>
        <div class='col-md-4'>
            {!! Form::bsText('min_width','Min width(px)  ') !!}
            {!! Form::bsText('max_width','Max width(px)  ') !!}
            {!! Form::bsText('min_prop','Min',10) !!}
            {!! Form::bsText('validate','Validate (0 .. 1) %  ',0.1,['min'=>0, 'max'=>1, 'step'=>0.01]) !!}
            {!! Form::bsSelect('user_id','User',$user_ids,0) !!}
            {!! Form::bsSelect('crop_form','Crop form',__('common.crop_from'),0) !!}
            {!! Form::hidden('type',App\Dataset\Build::CLASSIFIER) !!}

            {!! Form::submit('Build',['class' => 'btn btn-primary','id'=>'build', 'disabled']) !!}
        </div>
    </div>
    {!! Form::close() !!}
    
@endsection

@section('page-js-script')

    <script>
        function checkBuildButton(){
            $checkboxes = $('#tree input[type="checkbox"][name*="tag"]');
            
            var disable = true;
            $checkboxes.each(function(index,item){
                if ( $(item).prop('checked') ){
                    disable = false;
                } 
            });
            $("#build").prop('disabled', disable);
        }
        
       afterUpdate = checkBuildButton;
    </script>
@endsection