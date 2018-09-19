@extends('layout.common')

@section('sidebar')
    
@endsection

@section('content')
    <h2>Properties list</h2>
    
    <div class="container">
        <div class="row">
            <ul>
            @foreach($properties as $property) 
                <li> 
                    {{ $property->name }} ( {{ implode(",",App\Http\Controllers\Controller::collection2select($property->tags)) }} )
                    <a href="{{ route('properties.edit',$property->id) }}" title="Edit"><i class="fas fa-edit"></i></a>
                    {!! Html::deleteLink(route('properties.destroy',$property->id)) !!}
                </li>
            @endforeach
            </ul>
        </div>
    </div>    
    <hr>
    <br>
    <a href="{{route('properties.create')}}" role="button" class="btn btn-primary">Add</a>
    
@endsection
