@extends('layout.common')

@section('sidebar')
    
@endsection

@section('content')
    <h2>Brands list</h2>
    
    <div class="container">
        <div class="row">
            <ul>
            @foreach($brands as $brand) 
                <li> 
                    {{ $brand->name }}
                    <a href="{{ route('brands.edit',$brand->id) }}" title="Edit"><i class="fas fa-edit"></i></a>
                    {!! Html::deleteLink(route('brands.destroy',$brand->id)) !!}
                </li>
            @endforeach
            </ul>
        </div>
    </div>    
    <hr>
    <br>
    <a href="{{route('brands.create')}}" role="button" class="btn btn-primary">Add</a>
    
@endsection
