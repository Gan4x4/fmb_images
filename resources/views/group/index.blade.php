@extends('layout.common')

@section('sidebar')

@endsection

@section('content')
    <h2>Groups list</h2>
    
    <div class="container">
        <div class="row">
            <ul>
            @foreach($groups as $group) 
                <li> 
                    {{ $group->name }}
                    <a href="{{ route('groups.edit',$group->id) }}" title="Edit"><i class="fas fa-edit"></i></a>
                    {!! Html::deleteLink(route('groups.destroy',$group->id)) !!}
                </li>
            @endforeach
            </ul>
        </div>
    </div>    
    <hr>
    <br>
    <a href="{{route('groups.create')}}" role="button" class="btn btn-primary">Add</a>
    
@endsection
