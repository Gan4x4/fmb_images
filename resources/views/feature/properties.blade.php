@php
     $selectable = $properties->filter( function($property,$key){
        return ! $property->isManualInput();
      });

      $manual_input = $properties->filter( function($property,$key){
        return $property->isManualInput();
      });

@endphp

@if( isset($info))
    <p class="small">{{ $info }}</p>
@endif
@foreach($selectable as $p)
    @php
        $tagArray = App\Http\Controllers\Controller::collection2select($p->tags);
        //$tagArray[0] = "---";
        $tagArray =[0=>'-'] + App\Http\Controllers\Controller::collection2select($p->tags);
        if ( $p->isSearchable() ){
            $attr = ['class'=>'make_selectized'];
        }else{
            $attr = [];
        }
        
        $title = $p->name;
        if ($p->estimation_source){
            $title .= ' ('.$p->estimation_source.')';
        }
        
    @endphp
    
   
    {!! Form::bsSelect('property_'.$p->id,$title,$tagArray,$p->tagId(),$attr) !!}
    
@endforeach


@foreach($manual_input as $p)

    @php
        $strings = array_values( App\Http\Controllers\Controller::collection2select($p->tags) );
        $json = json_encode($strings);
        $title = $p->name;
        if ($p->estimation_source){
            $title .= ' ('.$p->estimation_source.')';
        }
    @endphp
    
    {!! Form::bsText('manual_property_'.$p->id,$title,$p->getTagName(),['class' => 'tag_input', 'data' =>$json ]) !!}
    
@endforeach