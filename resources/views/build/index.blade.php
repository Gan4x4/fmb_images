@extends('layouts.common')

@section('sidebar')

    ----
    
@endsection


@section('content')

    <h2>Builds</h2>
    <table class='table'>
        <tr>
        <th>Time</th>
        <th>Description</th>
        <th>State</th>
        
        </tr>
        @foreach($builds as $build)
        <tr>
            <td>
                {{ $build->updated_at }}
            </td>
            <td>
                {{ $build->description }}
            </td>
            <td>
                {{ $build->getStateName() }}
            </td>
            
            <td>
                <a href="{{ $build->getLink() }}"><i class="fas fa-download"></i></a>
            </td>
            <td>
             {!! Html::deleteLink(route('builds.destroy',$build->id)) !!}
            </td>
        </tr>
        @endforeach
    </table>
    
    
@endsection
