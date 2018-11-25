@extends('layouts.common')

@section('sidebar')

@endsection


@section('content')
    <h2>Users</h2>
    
    <div class="container">
        <div class="row">
            <ul>
            @foreach($users as $user) 
            @php( $count = $user->getStat())
                <li> 
                    <a href="{{ route('users.show',$user->id) }}" >{{ $user->name }}</a> 
                    images:{{ $count['images'] }} / items: {{ $count['features'] }} tags/ {{ $count['properties']}}
                    
                </li>
            @endforeach
            </ul>
        </div>
    </div>    
    
@endsection
