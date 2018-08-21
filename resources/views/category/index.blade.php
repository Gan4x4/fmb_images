@extends('layout.common')

@section('sidebar')

    
    
@endsection

@section('content')
    <h2>Category list</h2>
    
    <div class="container">
        <div class="row">
            <ul>
            @foreach($categorys as $cat) 
                <li> 
                    {{ $cat->name }}
                    <a href="{{ route('tags.edit',$cat->id) }}" title="Edit"><i class="fas fa-edit"></i></a>
                    {!! Html::deleteLink(route('tags.destroy',$cat->id)) !!}
                </li>
            @endforeach
            </ul>
        </div>
    </div>    
    <hr>
    <br>
    <a href="{{route('tags.create')}}" role="button" class="btn btn-primary">Add</a>
    
@endsection
