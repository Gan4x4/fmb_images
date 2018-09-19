<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Items filter</span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
              </a>
        </h6>
        
        
        {!! Form::open(['route'=>['build'],'method'=>'get']) !!}
            @include('item.tree',['items'=>$items])
            {!! Form::submit('Build') !!}
        {!! Form::close() !!}
      
    

    
    
    
    
    
  
    
    
    
    
    
    
    
    
    
    
    
    
    
     
     
        
    </nav>  
     
