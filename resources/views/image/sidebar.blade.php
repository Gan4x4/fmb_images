<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        
        @if (Auth::user()->isAdmin() )
            
            @php
                $tabs= [
                    route('images.index') => 'Stat',
                    route('images.index',['filter'=>1,'new'=>$active_tab]) => 'Filter'
                ];
            @endphp
        
        
            {!! Html::bsTabs($tabs, intval($enable_filter) ) !!}       
            
            @if ($enable_filter)
        
                {!! Form::open(['route' => ['images.index'],'method'=>'get']) !!}

                    {!! Form::hidden('filter',$enable_filter) !!}
                    {!! Form::hidden('new',$active_tab) !!}
                    
                    @include('item.tree',['items'=>$items])
                    <hr>
                    {!! Form::submit("Apply") !!}
                {!! Form::close() !!}
            @else
                @include('user.stat')
            
            @endif
            
        @else
        
            @include('user.stat')
        
        @endif
    </div>
</nav>  
     
