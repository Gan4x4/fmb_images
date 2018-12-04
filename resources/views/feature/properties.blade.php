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
    
        $jq_id = 'property_'.$p->id;
        
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
        

        $hints = []; //$p->getPopularTags(5);
        foreach($p->getPopularTags() as $t){
            if ($p->isSearchable()){
                $hint_js = "$('#".$jq_id."')[0].selectize.addItem('".$t->id."')"; 
            }
            else{
                $hint_js = "$('#".$jq_id."').val(".$t->id.")";
            }
            $hints[$hint_js] = $t->name;
        }
        
    @endphp
    
   
    {!! Form::bsSelectHinted($jq_id,$title,$tagArray,$p->tagId(),$hints,$attr) !!}
    
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