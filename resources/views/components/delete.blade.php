@php
    $attributes = "";
    foreach($attr as $k=>$v){
        $attributes .= "$k = '$v' ";
    }

@endphp

<a href="javascript:void(0)" $attributes onClick="deleteEntity('{{ $route }}','{{ $message }}')"><i class="fas fa-trash"></i></a>



                                
