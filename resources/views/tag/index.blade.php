@extends('layout.common')

@section('sidebar')

    
    
@endsection

@section('content')
    <h2>Tags list</h2>
    
    <div class="container">
        <div class="row">
            <ul>
            @foreach($tags as $tag) 
                <li> 
                    {{ $tag->name }}
                    <a href="{{ route('tags.edit',$tag->id) }}" title="Edit"><i class="fas fa-edit"></i></a>
                    {!! Html::deleteLink(route('tags.destroy',$tag->id)) !!}
                </li>
            @endforeach
            </ul>
        </div>
    </div>    
    <hr>
    <br>
    <a href="{{route('tags.create')}}" role="button" class="btn btn-primary">Add</a>
    
@endsection
