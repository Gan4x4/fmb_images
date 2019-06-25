
@foreach ($count as $key=>$cnt)
<h2>{{ $key }}</h2>
    Images {{ $cnt['images'] }}
    <br>
    Features {{ $cnt['features'] }}
    <br>
    Filled properties {{ $cnt['properties'] }}
@endforeach
