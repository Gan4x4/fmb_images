
    <form id="feature_form">
        {!! Form::hidden('feature_id',$feature->id,['id'=>'feature_id']) !!}

        {!! Form::bsSelect('item_id','Item',$items,$item_id,['id'=>'item_id']) !!}
        
        <div id="property_block">
            @include('feature.properties')
        </div>
        
        <a class="btn btn-primary" id="save_feature" class="btn btn-default" {{ $disable_save ? 'disabled' : ''  }} >Save</a>
        <a class="btn btn-light" id="delete_feature" class="btn btn-default">Delete</a>
        
        <hr>
        Coordinates
        <div class="form-row ">
            <div class="form-group col-md-6">
                 <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">x1</div>
                    </div>
                    <input type="number"  name="x1" class='coordinate form-control' id="x1" min="0" max="{{ $image->width - 1 }}"  value="{{ $feature->x1 ? $feature->x1 : 0  }}" >
                </div>
            </div>
            <div class="form-group  col-md-6 ">
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">y1</div>
                    </div>
                    <input type="number"  name="y1" class='coordinate form-control' id="y1" min="0" max="{{ $image->height - 1 }}" value="{{ $feature->y1 ? $feature->y1 : 0  }}" >
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">x2</div>
                    </div>
                    <input type="number"  name="x2" class='coordinate form-control' id="x2" min="0" max="{{ $image->width - 1 }}" value="{{ $feature->x2 ? $feature->x2 : $image->width - 1 }}" >
                </div>
            </div>
            <div class="form-group col-md-6">
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">y2</div>
                    </div>
                    <input type="number"  name="y2" class='coordinate form-control' id="y2" min="0" max="{{ $image->height - 1 }}"  value="{{ $feature->y2 ? $feature->y2 : $image->height - 1  }}" >
                </div>
            </div>
        </div>
    </form>


