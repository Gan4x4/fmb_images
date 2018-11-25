
        
    <div class="row">
        @foreach($images as $image) 
            <div class="col-md-3 d-flex"  >
                <div class="card card-body "  >

                    @php
                        if ( Auth::user()->isAdmin() || $image->user){

                            $route = route( "images.edit",$image->id);
                        }else{
                            $route = route( "images.show",$image->id);
                        }

                    @endphp

                    <a href='{{ $route }}'>
                        <img class="card-img-top" src="{{ $image->getThumbUrl() }}" alt="Card image cap">
                    </a>
                    <div class="card-body">
                        <!--
                        <p class="card-text">{{ $image->description }}</p>

                        <a href="{{route('images.edit',[$image->id])}}" class="card-link">Edit</a>
                        -->

                        @foreach($image->getFeatureDescription() as $item=>$props)
                            {{ $item }} : 
                            {{ implode(", ",$props) }}
                            <br>
                        @endforeach


                        <hr>
                        @if ( Auth::user()->isAdmin() && $image->user)
                            {{ $image->user->name }}

                        @endif   

                        @if( Auth::check() && ! $image->user)
                            {!! Form::open(['route' => ['images.take',$image->id],'method'=>'post']) !!}
                                {!! Form::submit('Take it') !!}
                            {!! Form::close() !!}

                        @endif


                        @if ($image->status == 0 )
                            <i class="fab fa-yelp"></i>
                        @endif

                        @if ($image->getSiblings()->count() )
                            <i class="fas fa-link"></i> {{ $image->getSiblings()->count() }}
                        @endif

                    </div>
                </div>
            </div>
        @endforeach


    </div>
    <hr>
    <div class='row '>
        <div class='col-lg-4 offset-lg-4  d-flex' >
        {{ $images->links() }}
        </div>
    </div>

