@extends('layouts.common')

@section('sidebar')

    ----
    
@endsection


@section('content')

    {{ Html::bsTabs($menu,3) }} 

    {!! Form::open(['route'=>['builds.store']]) !!}
    <div class='row'>
        <div id='tree' class='col-md-8'>
            Validation set size: {{ App\Image::where('validation',true)->where('user_id','>',0)->count() }} images
            @include('item.tree',['items'=>$items,'validation'=>true]) 
            
        </div>
        
        <div class='col-md-4'>
            {!! Form::hidden('type',App\Dataset\Build::VALIDATION) !!}
            
            {!! Form::bsText('max_width','Max width(px)  ') !!}
            {!! Form::bsCheckbox('subkeys',"Store subkeys",1,true) !!}

            {!! Form::submit('Build',['class' => 'btn btn-primary','id'=>'build']) !!}
        </div>
    </div>

     
    {!! Form::close() !!}
            
@endsection

