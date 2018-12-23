@extends('layouts.common')

@section('sidebar')

    ----
    
@endsection


@section('content')

    <h2>Builds</h2>
    <table>
        @foreach($builds as $build)
        <tr>
            <td>
                {{ $build->updated_at }}
            </td>
            
            <td>
                {{ $build->getStateName() }}
            </td>
            
            <td>
                <a href="{{ $build->getLink() }}">result</a>
            </td>
            
            
        </tr>
        @endforeach
    </table>
    
    
@endsection
