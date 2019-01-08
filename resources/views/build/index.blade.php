@extends('layouts.common')

@section('sidebar')

    ----
    
@endsection


@section('content')

    {{ Html::bsTabs($menu) }}

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
                @if( $build->getLink() )
                    <a href="{{ $build->getLink() }}"><i class="fas fa-download"></i></a>
                @endif
            </td>
            <td>
                @if( ! $build->isActive() )
                    {!! Html::deleteLink(route('builds.destroy',$build->id)) !!}
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    
    
@endsection
