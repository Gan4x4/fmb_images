@foreach($properties as $p)
    @php
        //$tagArray = App\Http\Controllers\Controller::collection2select($p->tags);
        //$tagArray[0] = "---";
        $tagArray = array_merge([0=>'---'],App\Http\Controllers\Controller::collection2select($p->tags))
    @endphp
    {!! Form::bsSelect('property_'.$p->id,$p->name,$tagArray,$p->tagId()) !!}
@endforeach