<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        
        @if (Auth::user()->isAdmin())
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                  <span>Build dataset</span>
                  <a class="d-flex align-items-center text-muted" href="#">
                    <span data-feather="plus-circle"></span>
                  </a>
            </h6>

            {!! Form::open(['route'=>['build'],'method'=>'get']) !!}
                @include('item.tree',['items'=>$items])
                <hr>
                {!! Form::bsCheckbox('subdirs','Subdirs',true) !!}
                
                {!! Form::bsText('min_prop','Min',10) !!}
                {!! Form::bsText('validate (0 .. 1)','Validate %',0.1,['min'=>0, 'max'=>1, 'step'=>0.01]) !!}
                {!! Form::bsSelect('type','Type',__('common.build_type')) !!}
                {!! Form::submit('Build') !!}
            {!! Form::close() !!}
            
        @else
        
            @include('user.stat')
        
        @endif
    </div>
</nav>  
     
