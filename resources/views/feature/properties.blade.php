@foreach($properties as $p)
    @php
        $tagArray = App\Http\Controllers\Controller::collection2select($p->tags);
        //$tagArray[0] = "---";
        $tagArray =[0=>'---'] + App\Http\Controllers\Controller::collection2select($p->tags);
        if ( $p->name == 'Brand'){
            $attr = ['class'=>'make_selectized'];
        }else{
            $attr = [];
        }
    @endphp
    {!! Form::bsSelect('property_'.$p->id,$p->name,$tagArray,$p->tagId(),$attr) !!}
@endforeach