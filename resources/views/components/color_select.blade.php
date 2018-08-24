<div class='form-group'>
    {!! Form::label($name, $title); !!}
    
    <select name="{{$name}}" id="{{ $name }}" class="form-control">
        @foreach($values as $color=>$name)
            <option style="appearance:none; -moz-appearance:none; -webkit-appearance:none; background-color: {{ $color }}" value="{{ $color }}" {{ $selected == $color ? 'selected' : '' }} >
                {{ $name }}
            </option>
        @endforeach
    </select>
    
</div>
