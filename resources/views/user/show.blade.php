@extends('layouts.common')

@section('sidebar')

<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        @include('user.stat')
    </div>
</nav>  
    
@endsection

@section('content')
    <div class="container-fluid ">
        
        <h2>User Image list</h2>

        @include('image.list')
        
    </div>  
        
@endsection
