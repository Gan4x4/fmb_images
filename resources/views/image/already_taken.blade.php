@extends('layouts.common')

@section('content')

    <img src="{{ $image->getThumbUrl() }}" >

    <h3>
    Image already owned by {{ $user->name }}
    </h3>
    <p>
        Please find <a href="{{ route('images.new.first') }}">another image</a>
    </p>
@endsection
