@extends('layouts.common')


@section('content')
    
    <table class="table">
        @foreach($lines as $line)
            <tr>    
                <td><a href="{{ $line['url'] }}">{{ $line['url'] }}</a></td>
                <td><a href="{{ $line['image'] }}">{{ $line['image'] }}</a></td>
                <td>{{ $line['info'] }}</td>
            </tr>
        @endforeach
    </table>

@endsection