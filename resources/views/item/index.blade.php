@extends('layout.common')

@section('sidebar')

@endsection

@section('content')
    <h2>Items list</h2>
    
    <div class="container">
        <div class="row">
            <ul>
            @foreach($items as $item) 
                <li> 
                    {{ $item->name }}
                    <a href="{{ route('items.edit',$item->id) }}" title="Edit"><i class="fas fa-edit"></i></a>
                    {!! Html::deleteLink(route('items.destroy',$item->id)) !!}
                </li>
            @endforeach
            </ul>
        </div>
    </div>    
    <hr>
    <br>
    <a href="{{route('items.create')}}" role="button" class="btn btn-primary">Add</a>
    
@endsection
