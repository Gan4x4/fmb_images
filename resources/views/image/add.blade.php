{!! Form::open(['route'=>'images.store','class'=>'form-inline'])  !!}
    <div class='row'>
        <div class='col-md-12'>
            @for ($i = 1; $i <= 12; $i++)
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="checkbox" name="image_nums[]" class="form-check-input" value="{{$i}}" {{ $i == 1 ? 'checked' : '' }}>{{$i}}
                    </label>
                </div>
            @endfor
        </div>
        <div class='col-md-12'>

                <label for="url" class="sr-only">URL</label>
                {!! Form::text('url',null,['class'=>'form-control', 'placeholder' => "URL",'size'=>70]) !!}

                {!! Form::submit("Parse",['class'=>'btn btn-primary ']) !!}    
        </div>
        
    </div>
{!! Form::close() !!}
