@extends('layouts.common')

@section('sidebar')

    ----
    
@endsection


@section('content')

    {{ Html::bsTabs($menu,1) }} 

    {!! Form::open(['route'=>['build'],'method'=>'get']) !!}
    <div class='row'>
        <div id='tree' class='col-md-8'>
            @include('item.tree',['items'=>$items])
        </div>
        <div class='col-md-4'>
            
            {!! Form::bsText('validate','Validate (0 .. 1) %  ',0.1,['min'=>0, 'max'=>1, 'step'=>0.01]) !!}
            {!! Form::hidden('type',App\Dataset\Build::DARKNET) !!}

            {!! Form::submit('Build',['class' => 'btn btn-primary','id'=>'build', 'disabled']) !!}
        </div>
    </div>

     
    {!! Form::close() !!}
            
@endsection

@section('page-js-script')

    <script>
        function checkBuildButton(){
            $checkboxes = $('#tree input[type="checkbox"][name*="items"]');
            
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